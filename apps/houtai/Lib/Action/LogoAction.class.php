<?php
class logoAction extends Action{
    public function Logo(){

        if(isset($_POST['submit'])){
            if ($_FILES["file"]["type"] == "image/png" && $_FILES["file"]["size"] < 90000)
            {
                if ($_FILES["file"]["error"] > 0)
                {
                    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                }
                else
                {
                    $_FILES['file']['name']=$GLOBALS['ts']['user']['school_id'].".png";
                    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
                    echo "Type: " . $_FILES["file"]["type"] . "<br />";
                    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
                    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

                    if (file_exists(SITE_PATH.'/apps/public/_static/image_school/' . $_FILES["file"]["name"]))
                    {
                        echo $_FILES["file"]["name"] . " already exists. ";
                    }
                    else
                    {
                        //$_FILES['file']['tmp_name'] = str_replace('\\\\', '\\', $_FILES['file']['tmp_name']);
                        move_uploaded_file($_FILES["file"]["tmp_name"],
                            SITE_PATH."/apps/public/_static/image_school/". $_FILES["file"]["name"]);
                        //sleep(5);
                        echo "Stored in:  " .SITE_PATH."/apps/public/_static/image_school/". $_FILES["file"]["name"];

                    }
                }
            }
            else
            {
                echo "Invalid file";
            }
        }
        $this->display();
    }
}
?>