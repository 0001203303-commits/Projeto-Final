@php
    $n = count($serie);
    $maxValor = collect($serie)->max('total');
    $max = max(1, $maxValor);
    $slot = 480 / $n;
    $largura = round($slot * 0.7, 1);
    $passo = $n <= 12 ? 1 : (int) ceil($n / 10);
    $destaque = ! empty($destacarMax)
        ? collect($serie)->pluck('total')->search($maxValor)
        : $n - 1;
@endphp

@if (collect($serie)->sum('total') === 0)
    <p class="sem-dados">Nenhuma triagem neste período.</p>
@else
    <svg viewBox="0 0 480 150" class="svg-barras" role="img" aria-label="{{ $descricao ?? 'Gráfico de barras' }}">
        @foreach ($serie as $i => $h)
            @php
                $alt = (int) round($h['total'] / $max * 100);
                $x = round($i * $slot + ($slot - $largura) / 2, 1);
                $cx = round($x + $largura / 2, 1);
            @endphp
            <rect x="{{ $x }}" y="{{ 120 - $alt }}" width="{{ $largura }}" height="{{ $alt }}" rx="3"
                  class="barra-svg {{ $i === $destaque ? 'atual' : '' }}"/>
            @if ($h['total'] > 0 && $n <= 12)
                <text x="{{ $cx }}" y="{{ 115 - $alt }}" text-anchor="middle" class="valor-svg">{{ $h['total'] }}</text>
            @endif
            @if ($i % $passo === 0)
                <text x="{{ $cx }}" y="138" text-anchor="middle" class="rotulo-svg">{{ $h['rotulo'] }}</text>
            @endif
        @endforeach
        <line x1="0" y1="120" x2="480" y2="120" class="eixo-svg"/>
    </svg>
@endif