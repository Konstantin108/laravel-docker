<h3>Data rows count: {{ $usersCount }}</h3>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
    <tr style="background-color: #cfd8dc; color: #37474f; text-align: left;">
        <th style="padding: 8px; border: 1px solid #b0bec5;">ID</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Name</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Email</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Reserve Email</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Phone</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Telegram</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Email Verified At</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Created At</th>
        <th style="padding: 8px; border: 1px solid #b0bec5;">Updated At</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $index => $user)
        <tr style="background-color: {{ $index % 2 === 0 ? '#eceff1' : '#f7fafc' }}; color: #263238;">
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->id }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->name }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->email }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->reserveEmail }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->phone }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->telegram }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->emailVerifiedAt }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->createdAt }}</td>
            <td style="padding: 8px; border: 1px solid #b0bec5;">{{ $user->updatedAt }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
