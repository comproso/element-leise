<div id="{{ $name }}" class="leise {{ $type }} variable ui @if($type == 'manifest') blue @endif card">
	<div class="content">
		<i class="right floated inverted circular {{ $icon }} icon"></i>
		<h4 class="header">{{ trans($label) }}</h4>
		<div class="meta">
			<span class="abstract">TBD</span>
		</div>
		<div class="graph content">
			<h5 class="ui header">{{ trans('Verlauf') }}</h5>
			<canvas id="{{ $name }}_graph" height="150"></canvas>
		</div>
		<div class="extra content">
			<div class="ui mini statistic">
				<span class="value">{{ $value }}</span>
				<span class="label">{{ trans($unit) }}</span>
			</div>
			@if($type == 'manifest')
			<div class="data details">
				<input type="range" name="{{ $name }}" value="{{ $value }}" @if(isset($min)) min="{{ $min }}" @endif @if(isset($max)) max="{{ $max }}" @endif @if(isset($scale)) steps="{{ $scale }}" @endif data-value="{{ json_encode([$value]) }}">
			</div>
			@else
			<input type="hidden" name="{{ $name }}" value="{{ $value }}" data-value="{{ json_encode([$value]) }}">
			@endif
		</div>
	</div>
</div>