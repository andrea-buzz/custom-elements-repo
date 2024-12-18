<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <title>PHP Custom Element Form Builder</title>
        <script src="./custom-form-1.js?v=<?php echo filesize('./custom-form-1.js')?>"></script>
        <style>
        body {
                font-family: sans-serif;
                font-size: 16px;
                margin: 0;
                color: #333;
            }
            custom-form-1 > form {
                display: flex;
                flex-direction: column;
            }
            code {
                display: block;
                max-width: 100%;
                overflow: hidden;
            }
            @media screen and (min-width: 992px){
                custom-form-1 > form {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                }   
            }
        </style>
    </head>
    <body><?php 
    $data_fields = [
        ['tagname' => 'input', 'type' => 'text', 'name' => 'first-name', 'placeholder' => 'il tuo nome', 'value' => isset( $_POST['first-name'] )? $_POST['first-name']:'', 'label' => 'Nome', 'required'=> true, 'error' => 'Il campo "Nome" deve essere compilato correttamente.', 'valid'=>validate_input( 'first-name', true ) ],
        ['tagname' => 'input', 'type' => 'text', 'name' => 'last-name', 'placeholder' => 'il tuo cognome', 'value' => isset( $_POST['last-name'] )? $_POST['last-name']:'', 'label' => 'Cognome', 'required' => true, 'error' => 'Il campo "Cognome" deve essere compilato correttamente.', 'valid'=>validate_input( 'last-name', true ) ],
        ['tagname' => 'input', 'type' => 'date', 'name' => 'birth-date', 'placeholder' => 'gg/mm/aaaa', 'value' => isset( $_POST['birth-date'] )? $_POST['birth-date']:'', 'label' => 'Data di nascita', 'required' => true, 'error' => 'Il campo "Data di nascita" deve essere una data valida.', 'valid'=>validate_input( 'birth-date', true ) ],
        ['tagname' => 'select', 'name' => 'week-day[]', 'value' => isset( $_POST['week-day'] )? $_POST['week-day']:'', 'label' => 'Giorno della settimana', 'required' => false, 'multiple' => true,
            'options' => [
                ['value' => '', 'text' => 'Selezionare il giorno'], 
                ['value' => 'lun', 'text' => 'Lunedì'],
                ['value' => 'mar', 'text' => 'Martedì'], 
                ['value' => 'mer', 'text' => 'Mercoledì'],
                ['value' => 'gio', 'text' => 'Giovedì'], 
                ['value' => 'ven', 'text' => 'Venerdì'],
                ['value' => 'sab', 'text' => 'Sabato'],
                ['value' => 'dom', 'text' => 'Domenica'] ], 
            'error' => 'Selezionare ua voce nel campo "Giorno della settimana".', 'valid'=>validate_input( 'week-day', true ) ],
        ['tagname' => 'input', 'type' => 'file', 'name' => 'attachment1', 'value' => null, 'label' => 'Carica un file', 'required'=> true, 'error' => 'Il File non è stato caricato.', 'valid'=>validate_input_file( 'attachment1', true ) ],
        ['tagname' => 'textarea', 'name' => 'message', 'value' => isset( $_POST['message'] )? $_POST['message']:'', 'label' => 'Messaggio', 'required'=> true, 'error' => 'Il campo "Messaggio" deve essere compilato.', 'valid'=>validate_input( 'message' ), true ],
        ];
    ?>
        <custom-form-1 action="" method="post" enctype="multipart/form-data" data-fields="<?php echo htmlspecialchars( json_encode( $data_fields ) )  ?>"></custom-form-1>
        <div><?php
        $enable_post_data_reading = ini_get('enable_post_data_reading');
        
        
    if (isset($_POST['submit'])) {
        
        printf('<code>%s</code>', json_encode( $_POST ) );
        printf('<code>%s</code>', json_encode( $_FILES ) );
        $currentDirectory = getcwd();
        $uploadDirectory = "/uploads/";

        $errors = []; // Store errors here

        $fileExtensionsAllowed = [ 'svg', 'jpeg', 'jpg', 'png', 'webp' ]; // These will be the only file extensions allowed 
        if( isset( $_FILES['attachment1'] ) && 0 < $_FILES['attachment1']['size']){
            $fileName = $_FILES['attachment1']['name'];
            $fileSize = $_FILES['attachment1']['size'];
            $fileTmpName  = $_FILES['attachment1']['tmp_name'];
            $fileType = $_FILES['attachment1']['type'];
            $fileExtension = strtolower(end(explode('.',$fileName)));
            if (! in_array($fileExtension,$fileExtensionsAllowed)) {
                $errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";
            }
            $uploadPath = $currentDirectory . $uploadDirectory . basename($fileName); 
        
            if ($fileSize > 1000000) {
                $errors[] = "File exceeds maximum size (1MB)";
            }

            if (empty($errors)) {
                $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

                if ($didUpload) {
                    printf(' <div class="success">%s</div>', "The file " . basename($fileName) . " has been uploaded" );
                } else {
                    printf(' <div class="warning">%s</div>', "An error occurred. Please contact the administrator." );
                }
            } else {
                foreach ($errors as $error) {
                printf(' <div class="error">%s</div>', $error  . "\n"); //  . " These are the errors" . "\n"
                }
            }
        }

    }
    function validate_input( $field_name, $required = false ){
        $valid = true;
        if( $required && isset( $_POST[ $field_name ] ) ){
            switch( $field_name ){
                case 'first-name':
                    $valid = preg_match('/^[\D]+$/i', $_POST[ $field_name ]);
                break;
                case 'last-name':
                    $valid = preg_match('/^[\D]+$/i', $_POST[ $field_name ]);
                break;
                case 'birth-date':
                    if( is_array( $_POST[ $field_name ] ) ){
                        $valid = (bool) count( $_POST[ $field_name ] );
                    }else{
                        $valid = preg_match('/^\d\d\d\d-\d\d-\d\d$/', $_POST[ $field_name ]);
                    }
                    
                break;
                case 'message':
                    $valid = preg_match('/^.+$/', $_POST[ $field_name ]);
                break;
            }
        }
        return $valid;
    }

    function validate_input_file( $field_name, $required = false ){
        $valid = isset($_POST['submit'])?false:true;
        if( $required && isset( $_FILES[ $field_name ] ) ){
            $valid = isset( $_FILES[ $field_name ]['size'] ) && (bool) $_FILES[ $field_name ]['size'] ;
        }
        return $valid;
    }
?>      </div>
    </body>
</html>