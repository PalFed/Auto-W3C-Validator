<?php
include_once 'validator.config.php';

class validator
{
    /* Internal variables to store the status */
    private $Warnings=0;
    private $Errors=0;
    private $Status="";
    private $JSON="";
    private $FullHTML;
    private $ResponseCode;

    /* Call this function to run the validation on the HTML passed in.
        Returns a negative number if there is an error.
        Otherwise, if $Summarize is set to true, then it will return an HTML
        summary. If Summarize is false then ?
     */
    function validate($HTML, $Doctype, $Summarize=true)
    {

        // Get the results
        if (!$this->getResults($Doctype.$HTML)) return false;

        $print="";

        // What is the status? Set the class.
        if ($this->Status=="Invalid") $class="w3c-status-invalid";
        else if ($this->Status=="Valid" && $this->Warnings>0) $class="w3c-status-warnings";
        else if ($this->Status=="Valid") $class="w3c-status-valid";
        else if ($this->Status=="Abort") $class="w3c-status-abort";
        else
        {
            if (!preg_match('/^HTTP/1.1 200/', $this->ResponseCode))
            {
                if ($this->ResponseCode=="") $this->ResponseCode="HTTP error retrieving result.";
                $class="w3c-status-http-error";
                $this->Status= $this->ResponseCode." from ".ValidatorHost.ValidatorPath."<br>";
            }
            else
            {
                $class="w3c-status-unknown";
                $this->Status.=" ".$this->ResponseCode." ";
                $this->Status= "Unknown result from ".ValidatorHost.ValidatorPath." : [".$this->Status."]<br>";
            }
        }

        if ($Summarize && ($this->Status!="Valid" || $this->Warnings))
        {
            $print.= "<div class='w3c-status-wrapper $class'>";
            $print.= "W3C Validation: ";
            if ($this->Status=="Invalid") $print.= $this->Status." (".$this->Errors." errors, ".$this->Warnings." warnings). ";
            else if (trim($this->Status)=="") $print.= "HTTP error retrieving result. ";
            else if (trim($this->Status)=="Abort") $print.= "Sorry! This document cannot be checked. ";
            else $print.=$this->Status." ";
            $print.="<a href='javascript:void(0)' onclick='w3c_validate(\"$Doctype\")' class='w3c-recheck-validation'>Re-check</a><br><br>";
            $arr=json_decode($this->JSON);
            $print.= "<ul>";
            if (is_object($arr) && isset($arr->messages) && is_array($arr->messages))
            {
                foreach ($arr->messages as $obj)
                {
                    if ($obj->type!="info")
                    {
                        $print.= "<li><span title=\"".htmlentities($this->get_fragment($HTML, $obj->lastLine, $obj->lastColumn))."\">";
                        if ($obj->type=="error") $print.= "ERROR: ";
                        else $print.= $obj->type.": ";
                        $print.= $obj->message." (line ".$obj->lastLine." col ".$obj->lastColumn.")</span></li>";
                    }
                }
            }
            $print.= "</ul>";
            $print.= "</div>";
        }
        else if ($Summarize && $this->Status=="Valid")
        {
            $print.= "<div class='w3c-status-wrapper $class'>W3C Validation: Valid <a href='javascript:void(0)'  onclick='w3c_validate(\"$Doctype\")' class='w3c-recheck-validation'>Re-check</a></div>";
        }
        else if (!$Summarize)
        {
            // Return
            $print=$this->JSON;
        }

        return $print;
    }

    private function getResults($HTML)
    {
        $req="doctype=Inline&output=json&fragment=".urlencode($HTML);
        $header = "POST ".ValidatorPath."/check HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= 'Host: ' . ValidatorHost . "\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";


        if (ValidatorUseSSL) $fp = fsockopen ('ssl://'.ValidatorHost, ValidatorPort, $errno, $errstr, 30);
        else $fp = fsockopen (ValidatorHost, ValidatorPort, $errno, $errstr, 30);

        if (!$fp)
        {
            // HTTP/s ERROR

            return false;
        }
        else
        {
            if (!fputs ($fp, $header . $req)) return false;
            $this->ResponseCode=false;
            $jsonstarted=false;
            while (!feof($fp))
            {
                $validationresult = fgets ($fp, 4096);
                if (!$this->ResponseCode) $this->ResponseCode=$validationresult;

                $this->FullHTML.=$validationresult;
                if (!$this->Status && preg_match("/^\s*X-W3C-Validator-Status:\s*(.+?)\s*$/", $validationresult, $matches)) {
                    $this->Status=$matches[1];
                }
                if ($this->Errors==0 && preg_match("/^\s*X-W3C-Validator-Errors:\s*(.+?)\s*$/", $validationresult, $matches)) {
                    $this->Errors=$matches[1];
                }
                if ($this->Status=="Abort")
                {
                    $this->Errors="Sorry! This document cannot be checked.";
                }

                if (preg_match("/<span class=\"err_type\">Warning<\/span>/", $validationresult)) $this->Warnings++;

                if (preg_match("/^\s*$/", $validationresult))
                {
                    // Start of json
                    $jsonstarted=true;
                }
                if ($jsonstarted)
                {
                    $validationresult=preg_replace('/"message": ,/', '"message": "",', $validationresult);
                    $this->JSON.=$validationresult;
                }

                //print $validationresult;

            }
            fclose ($fp);
        }
        return true;
    }


    /**
     * function get_fragment - return a snippet of text
     * @param $text - the text
     * @param $line_number, line number to start at
     * @param $column_number, column number to start at
     * @return string
     */
    function get_fragment($text, $line_number, $column_number)
    {
        $arr=preg_split("/\n/", $text);
        $line=($arr[$line_number-1]);

        $start=$column_number-SNIPPET_LENGTH;
        if ($start<0) $start=0;

        $chars=(SNIPPET_LENGTH*2)+1;

        $return= substr($line, $start, $chars);
        return $return;
    }

}
