<?php

namespace Comproso\Elements\Leise\Models;

use Illuminate\Database\Eloquent\Model;
#use Illuminate\Http\Request;

use Request;
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
		if(!isset($cache))
			$val = (isset($this->start_value)) ? $this->start_value : 0;
		else
			$val = $cache;

	    // prepare result
	    if(!Request::wantsJson())
	    {
		    $result = [
			    'type' => 'leise_element',
			    'label' => $this->label,
			    'unit' => (is_null($this->unit)) ? "" : $this->unit,
			    'scale' => $this->scale,
			    'type' => $this->variable_type,
			    'icon' => $this->icon,
			    'target_min' => round($this->target_value_min, $this->decimal),
			    'target_max' => round($this->target_value_max, $this->decimal),
		    ];

		    // set min
		    if($this->maximum !== null)
		    	$result['min'] = round($this->minimum, $this->decimal);

		    // set max
		    if($this->maximum !== null)
		    	$result['max'] = round($this->maximum, $this->decimal);
		}
		else
			$result['accessors'] = ['value'];

		// store result
		$result['value'] = round($val, $this->decimal);

	    // create Element response
	    return new LeiseElement($result);
    }

    // model proceeding
    public function proceed($cache = null)
    {
	    // differ variable types
		if($this->variable_type == "manifest")
		{
			if((isset($cache)) AND (is_numeric($cache)))
				$value = $cache;
			else
				$value = (isset($cache)) ? $cache : $this->start_value;
		}
		else
			$value = Parser::solve($this->formula, $this->allValues());

		// check for minimum violations
		if(($this->minimum !== null) AND ($value < $this->minimum))
			$value = $this->minimum;

		// check for maximum violations
		if(($this->maximum !== null) AND ($value > $this->maximum))
			$value = $this->maximum;

		// update session
		$session = Session::put('page_items.'.$this->items()->first()->id, $value);
		#Session::put('page_items', array_merge($session, [ => $value]));

		// return results
		return $value;
    }

	// get all values
	public function allValues()
	{
		// get items
		$items = Item::where('page_id', Session::get('page_id'))
								->where('element_type', 'Comproso\Elements\Leise\Models\LeiseElement')
								->orderBy('position')
								->get();

		// get session
		$session = Session::get('page_items');

		// prepare values
		$values = [];

		foreach($items as $item)
		{
			if(($item->element->variable_type == "manifest") AND (Request::has('item'.$item->id)) AND (is_numeric(Request::input('item'.$item->id))))
				$value = Request::input('item'.$item->id);
			elseif(isset($session[$item->id]))
				$value = $session[$item->id];
			else
				$value = ($item->start_value !== null) ? $item->start_value : 0;

			$values[$item->element->variable_name] = $value;
		}

		// return values
		return $values;
	}

    // finish
    public function finish()
    {
	    #if(Session::has('leise_variables'))
	    #	Session::forget('leise_variables');
    }

    // model template
    public function template()
    {
	    return "leise::variable";
    }
}
