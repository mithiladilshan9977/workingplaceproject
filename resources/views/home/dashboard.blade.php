<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000; /* Black background */
            color: #fff;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #111;
        }
        th, td {
            padding: 12px;
            border: 1px solid #333;
            text-align: left;
        }
        th {
            background-color: #222;
        }
        tr:nth-child(even) {
            background-color: #1a1a1a;
        }
        h1 {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h1>Logged In Users</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->status == 1 ? 'Active' : 'Inactive' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
