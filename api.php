<?php
require_once 'User.php';

header( 'Content-Type: application/json' );

$user = new User();
$response = [ 'success' => false, 'message' => '', 'errors' => [], 'data' => null ];

function validateUserInput( $data, $file = null ) {
    $errors = [];

    // Name validation
    if ( empty( $data[ 'name' ] ) ) {
        $errors[ 'name' ] = 'Name is required.';
    } elseif ( !preg_match( '/^[a-zA-Z\s]+$/', $data[ 'name' ] ) ) {
        $errors[ 'name' ] = 'Name must only contain letters and spaces.';
    }

    // Email validation
    if ( empty( $data[ 'email' ] ) ) {
        $errors[ 'email' ] = 'Email is required.';
    } elseif ( !filter_var( $data[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
        $errors[ 'email' ] = 'Invalid email format.';
    }

    // Contact validation
    if ( empty( $data[ 'contact' ] ) ) {
        $errors[ 'contact' ] = 'Contact is required.';
    } elseif ( !preg_match( '/^\d{10,15}$/', $data[ 'contact' ] ) ) {
        $errors[ 'contact' ] = 'Contact must be 10-15 digits.';
    }

    // Photo validation
    if ( $file && $file[ 'error' ] !== UPLOAD_ERR_NO_FILE ) {
        $allowedTypes = [ 'image/jpeg', 'image/png', 'image/gif' ];
        if ( !in_array( $file[ 'type' ], $allowedTypes ) ) {
            $errors[ 'photo' ] = 'Invalid photo format. Allowed formats: JPEG, PNG, GIF.';
        }

        if ( $file[ 'size' ] > 2 * 1024 * 1024 ) {
            $errors[ 'photo' ] = 'Photo size must not exceed 2MB.';
        }
    } elseif ( !$file || $file[ 'error' ] === UPLOAD_ERR_NO_FILE ) {
        $errors[ 'photo' ] = 'Photo is required.';
    }

    return $errors;
}

try {
    switch ( $_POST[ 'action' ] ) {
        case 'create':
        $errors = validateUserInput( $_POST, $_FILES[ 'photo' ] );
        if ( !empty( $errors ) ) {
            $response[ 'errors' ] = $errors;
            break;
        }

        $photo = null;
        if ( isset( $_FILES[ 'photo' ] ) && $_FILES[ 'photo' ][ 'error' ] === UPLOAD_ERR_OK ) {
            $photo = time() . '_' . basename( $_FILES[ 'photo' ][ 'name' ] );
            move_uploaded_file( $_FILES[ 'photo' ][ 'tmp_name' ], "uploads/$photo" );
        }

        $user->createUser( [
            'name' => $_POST[ 'name' ],
            'email' => $_POST[ 'email' ],
            'contact' => $_POST[ 'contact' ],
            'photo' => $photo
        ] );

        $response[ 'success' ] = true;
        $response[ 'message' ] = 'User created successfully';
        break;

        case 'read':
        $response[ 'success' ] = true;
        $response[ 'data' ] = $user->getAllUsers();
        break;

        case 'delete':
        $id = $_POST[ 'id' ];
        if ( empty( $id ) || !is_numeric( $id ) ) {
            $response[ 'message' ] = 'Invalid user ID.';
            break;
        }

        $user->deleteUser( $id );
        $response[ 'success' ] = true;
        $response[ 'message' ] = 'User deleted successfully';
        break;

        default:
        $response[ 'message' ] = 'Invalid action';
        break;
    }
} catch ( Exception $e ) {
    $response[ 'message' ] = $e->getMessage();
}

echo json_encode( $response );
?>
