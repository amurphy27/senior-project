
<?php
    include 'Field.php';
    require_once 'vendor/autoload.php';


    // Function takes an xml file with field objects and parses it into a list of field objects.
    function parse_xml_to_form($xml_file) {
        $xml = simplexml_load_file($xml_file) or die("Error: Cannot create object");
        $formType = (string) $xml->attributes();
        $formFields = array();

        foreach($xml->children() as $child)
        {
            $field = new Field;

            foreach($child->attributes() as $type => $attribute)
            {
                            
                switch ($type) {
                    case 'name':
                        $field->name = (string) $attribute;
                        break;
                    case "x":
                        $field->x = (double) $attribute;
                        break;
                    case "y":
                        $field->y = (double) $attribute;
                        break;
                    case "width":
                        $field->width = (int) $attribute;
                        break;
                    case "height":
                        $field->height = (int) $attribute;
                        break;
                    case "type":
                        $field->type = (string) $attribute;
                        break;
                }
            }
            array_push($formFields, $field);
        }

        $dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
        $dotenv->load();
        $servername = getenv('SERVERNAME');
        $username = getenv('USERNAME');
        $password = getenv('PASSWORD');

        try {
            $db = new PDO("mysql:host=$servername;dbname=paperwork", $username, $password);
            // set the PDO error mode to exception
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $stmt = $db->prepare("SELECT DISTINCT formType FROM templatefields");
        $stmt->execute();
        $existingTemplatesArray = $stmt->fetchAll();
        $insert = true;
        for ($i = 0; $i < count($existingTemplatesArray); $i++)
        {
            if (in_array($formType, $existingTemplatesArray[$i]))
            {
                $insert = false;
            }
        }
        if ($insert)
        {
            foreach($formFields as $field)
            {
                $stmt = $db->prepare("INSERT INTO templatefields (formType, name, xPos, yPos, width, height, type) VALUES (:formType, :fieldName, :fieldX, :fieldY, :fieldWidth, :fieldHeight, :fieldType)");
                $stmt->bindParam(':formType', $formType);
                $stmt->bindParam(':fieldName', $field->name);
                $stmt->bindParam(':fieldX', $field->x);
                $stmt->bindParam(':fieldY', $field->y);
                $stmt->bindParam(':fieldWidth', $field->width);
                $stmt->bindParam(':fieldHeight', $field->height);
                $stmt->bindParam(':fieldType', $field->type);
                $stmt->execute();
            }
        }
        else
        {
            echo "A form template by that name already exists remove it from the database or change the name of the new one.";
        }
    }
    //parse_xml_to_form($argv[1]);
    //parse_xml_to_form('XML/opsPlan.xml');
?>