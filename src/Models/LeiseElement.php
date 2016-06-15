<?php

namespace Comproso\Elements\Leise\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use Session;
use Validator;
use View;

use Comproso\Framework\Traits\ModelTrait;
use Comproso\Framework\Contracts\ElementContract;
use Comproso\Framework\Models\Item;

use jlawrence\eos\Parser;

class LeiseElement extends Model implements ElementContract
{
    use ModelTrait;

    // explanation table
    protected $table = 'leise_elements';

    // mass assignable (blacklist)
    protected $guarded = [];

    // JSON protecction
    protected $hidden = ['minimum', 'maximum', 'formula', 'start_value', 'decimal'];

	// Item model
    public function items()
    {
	    return $this->morphMany($this->ItemModel, 'element');
    }

	// model implementation
	public function implement($data)
	{
		// set TYPE
		$this->variable_type = $data->variable_type;

        // set variable type
        if($data->variable_type == "manifest")
        {
	        // set FORMULA
	        $this->formula = null;

        }
        else
        {
	        // set FORMULA
	        $this->formula = $data->formula;
        }

        // set FORM NAME
        #$this->form_name = (!isset($data->form_name)) ? "cleise_var_".$this->id : $data->form_name;

        // set VARIABLE NAME
        $this->variable_name = $data->variable_name;

        // set LABEL
        $this->label = $data->label;

        // set MINIMUM
        $this->minimum = $data->value_min;

        // set MAXIMUM
        $this->maximum = $data->value_max;

        // set START VALUE
        $this->start_value = $data->value_start;

        // set UNIT
        $this->unit = $data->unit;

        // set SCALE
        $this->scale = $data->scale;

        // set ICON
        $this->icon = $data->icon;

        // set DECIMAL
        $this->decimal = (isset($data->decimal)) ? $data->decimal : 0;

        // set TARGET VALUES
		$this->target_value_min = $data->target_value_min;
		$this->target_value_max = $data->target_value_max;


        // return true
        return true;
	}

    // model generation
    public function generate($cache = null)
    {
		// check if Session is prepared
	    if(!Session::has('leise_variables'))
		    $this->proceed($cache);

	    // get variables
	    $vars = Session::get('leise_variables');

	    // prepare result
	    $result = [
		    'label' => $this->label,
		    'value' => round($vars['values']['values'][$this->variable_name], $this->decimal),
		    'unit' => (is_null($this->unit)) ? "" : $this->unit,
		    'scale' => $this->scale,
		    'type' => $this->variable_type,
		    'icon' => $this->icon,
		    'target_min' => round($this->target_value_min, $this->decimal),
		    'target_max' => round($this->target_value_max, $this->decimal),
	    ];

	    // prepare attributes
	    $attributes = ['value'];

	    if($this->type == "manifest")
	    	$attributes[] = 'scale';

	    // set min
	    if(isset($vars['min']['values'][$this->variable_name]))
	    {
	    	$result['min'] = round($vars['min']['values'][$this->variable_name], $this->decimal);

	    	$attributes[] = 'min';
	    }

	    // set max
	    if(isset($vars['max']['values'][$this->variable_name]))
	    {
	    	$result['max'] = round($vars['max']['values'][$this->variable_name], $this->decimal);

	    	$attributes[] = 'max';
	    }

		// add accessors to result
	    $result['accessors'] = $attributes;

	    // create Element response
	    return new LeiseElement($result);
    }

    // model proceeding
    public function proceed($input)
    {
	    // get variable
	    if(!Session::has('leise_variables'))
	    {
		    // get item(s)
		    $items = Item::where('page_id', Session::get('page_id'))->where('element_type', 'Comproso\Elements\Leise\Models\LeiseElement')->orderBy('position')->get();

		    // prepare variable array
		    $vars = [
		    	'values' => ['values' => [], 'formulas' => []],
		    	'min'	=> ['values' => [], 'formulas' => []],
		    	'max'	=> ['values' => [], 'formulas' => []],
		    ];

		    // prepare start values
		    foreach($items as $item)
		    {
			    if(isset($item->element->start_value))
			    	$vars['values']['values'][$item->element->variable_name] = $item->element->start_value;
			    else
			    	$vars['values']['values'][$item->element->variable_name] = 0;

			    // set other values in array
			    if(isset($item->element->formula))
			    	$vars['values']['formulas'][$item->element->variable_name] = $item->element->formula;

			    // store type & co
			    $vars['types'][$item->element->variable_name] = $item->element->variable_type;
			    $vars['ids'][$item->element->variable_name] = $item->id;
			    $vars['forms'][$item->element->variable_name] = $item->element->form_name;
		    }

		    // set start values
		    foreach($items as $item)
		    {
			    // set start values
			    if(isset($item->element->formula))
			    	$vars['values']['values'][$item->element->variable_name] = Parser::solve($item->element->formula, $vars['values']['values']);

			    /*
				 *	MINIMUM
				 */

			    // store min
			    $vars['min']['formulas'][$item->element->variable_name] = $item->element->minimum;

			    // calc minimum
			    if($item->element->minimum !== null)
			    {
			    	$vars['min']['values'][$item->element->variable_name] = Parser::solve($item->element->minimum, $vars['values']['values']);
				    // see if variable minimum
				    if($vars['min']['values'][$item->element->variable_name] != $item->element->minimum)
				    	$vars['min']['formulas'][$item->element->variable_name] = $item->element->minimum;

				    // consider minimum
				    if($vars['values']['values'][$item->element->variable_name] < $vars['min']['values'][$item->element->variable_name])
				    	$vars['values']['values'][$item->element->variable_name] = $vars['min']['values'][$item->element->variable_name];
			    }

			    /*
				 *	MAXIMUM
				 */

			    // store max
			    $vars['max']['formulas'][$item->element->variable_name] = $item->element->maximum;

			    // calc maximum
			    if($item->element->maximum !== null)
			    {
			    	$vars['max']['values'][$item->element->variable_name] = Parser::solve($item->element->maximum, $vars['values']['values']);

				    // see if variable maximum
				    if((isset($vars['max']['values'][$item->element->variable_name])) AND ($vars['max']['values'][$item->element->variable_name] != $item->element->maximum))
				    	$vars['max']['formulas'][$item->element->variable_name] = $item->element->maximum;

				    // consider maximum
				    if((isset($vars['max']['values'][$item->element->variable_name])) AND ($vars['values']['values'][$item->element->variable_name] > $vars['max']['values'][$item->element->variable_name]))
				    	$vars['values']['values'][$item->element->variable_name] = $vars['max']['values'][$item->element->variable_name];
			    }
		    }

		    // store results in Session
		    Session::put('leise_variables', $vars);
	    }

	    // set session for proceeding
	    if(($this->variable_type == "latent") AND (!Session::has('leise_calc_id')))
	    	Session::put('leise_calc_id', $this->id);

	    // calculate variables
	    if((!isset($vars)) AND ($this->id === Session::get('leise_calc_id')))
	    {
		    // get Session variables
		    $vars = Session::pull('leise_variables');

			// calculate data
			foreach($vars['values']['values'] as $var_name => $value)
			{
				// solve formula if existing
				if(isset($vars['values']['formulas'][$var_name]))
					$vars['values']['values'][$var_name] = Parser::solve($vars['values']['formulas'][$var_name], $vars['values']['values']);

				// calc minimum
			    if(isset($vars['min']['formulas'][$var_name]))
			    	$vars['min']['values'][$var_name] = Parser::solve($vars['min']['formulas'][$var_name], $vars['values']['values']);

			    // consider minimum
			    if((isset($vars['min']['values'][$var_name])) AND ($vars['min']['values'][$var_name] > $vars['values']['values'][$var_name]))
			    	$vars['values']['values'][$var_name] = $vars['min']['values'][$var_name];

				// calc maximum
			    if(isset($vars['max']['formulas'][$var_name]))
			    	$vars['max']['values'][$var_name] = Parser::solve($vars['max']['formulas'][$var_name], $vars['values']['values']);

			    // consider maximum
			    if((isset($vars['max']['values'][$var_name])) AND ($vars['max']['values'][$var_name] < $vars['values']['values'][$var_name]))
			    	$vars['values']['values'][$var_name] = $vars['max']['values'][$var_name];

			}

			// store results in Session
			Session::put('leise_variables', $vars);
	    }

	    // get vars if not defined
	    if(!isset($vars))
	    	$vars = Session::get('leise_variables');

		// update mainfest variable value
	    if(($this->variable_type == "manifest") AND ($input != round($vars['values']['values'][$this->variable_name], $this->decimal)))
	    {
		    	$vars['values']['values'][$this->variable_name] = $input;

				// store session
				#Session::forget('leise_variables');			// TBD: constant variable list with changing only individual arrays/session vars
				Session::put('leise_variables', $vars);
	    }

	    #\Log::debug($this->variable_name);
	    #\Log::debug($input);
	    #\Log::debug($vars['values']['values'][$this->variable_name]);
	    #\Log::debug("=====================================");

	    // return variable value
	    return $vars['values']['values'][$this->variable_name];
    }

    // finish
    public function finish()
    {
	    if(Session::has('leise_variables'))
	    	Session::forget('leise_variables');
    }

    // model template
    public function template()
    {
	    return "leise::variable";
    }
}
