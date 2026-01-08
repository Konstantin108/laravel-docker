<h3>Data rows count: {{ $itemsCount }}</h3>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
    <tr style="background-color: #cfd8dc; color: #37474f; text-align: left;">
        @foreach(array_keys($items->first()->toArray()) as $key)
            <th style="padding: 8px; border: 1px solid #b0bec5; text-transform: uppercase;">{{ $key }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($items as $index => $item)
        <tr style="background-color: {{ $index % 2 === 0 ? '#eceff1' : '#f7fafc' }}; color: #263238;">
            @foreach($item->toArray() as $value)
                <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $value }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
