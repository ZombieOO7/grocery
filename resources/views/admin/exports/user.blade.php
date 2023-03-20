<table>
    <thead>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
     @forelse($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->status_text }}</td>
            <td>{{ $user->created_at }}</td>
        </tr>
    @empty
    @endforelse
    </tbody>
</table>
