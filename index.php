<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX CRUD with PHP</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>AJAX CRUD with PHP</h1>

    <form id="userForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        
        <label>Name: </label>
        <input type="text" name="name" id="name"><br>
        <span class="error-message" id="nameError"></span>

        <label>Email: </label>
        <input type="email" name="email" id="email"><br>
        <span class="error-message" id="emailError"></span>

        <label>Contact: </label>
        <input type="text" name="contact" id="contact"><br>
        <span class="error-message" id="contactError"></span>

        <label>Photo: </label>
        <input type="file" name="photo" id="photo"><br>
        <span class="error-message" id="photoError"></span>

        <button type="submit">Add User</button>
    </form>

    <h2>User List</h2>
    <table id="userTable" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        // Fetch and display all users
        function fetchUsers() {
            $.post('api.php', { action: 'read' }, function(response) {
                if (response.success) {
                    let rows = '';
                    response.data.forEach(user => {
                        rows += `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.contact}</td>
                                <td>${user.photo ? `<img src="uploads/${user.photo}" width="50">` : ''}</td>
                                <td><button onclick="deleteUser(${user.id})">Delete</button></td>
                            </tr>`;
                    });
                    $('#userTable tbody').html(rows);
                }
            });
        }

        // Delete user
        function deleteUser(id) {
            $.post('api.php', { action: 'delete', id: id }, function(response) {
                if (response.success) {
                    fetchUsers();
                } else {
                    alert(response.message);
                }
            });
        }

        // Handle form submission
        $('#userForm').submit(function(e) {
            e.preventDefault();

            // Clear previous error messages
            $('.error-message').text('');

            let formData = new FormData(this);

            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        fetchUsers();
                        $('#userForm')[0].reset();  // Reset form
                    } else {
                        // Display validation errors below each input
                        if (response.errors) {
                            if (response.errors.name) {
                                $('#nameError').text(response.errors.name);
                            }
                            if (response.errors.email) {
                                $('#emailError').text(response.errors.email);
                            }
                            if (response.errors.contact) {
                                $('#contactError').text(response.errors.contact);
                            }
                            if (response.errors.photo) {
                                $('#photoError').text(response.errors.photo);
                            }
                        }
                    }
                }
            });
        });

        $(document).ready(function() {
            fetchUsers();
        });
    </script>
</body>
</html>
