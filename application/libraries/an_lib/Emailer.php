<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

require 'vendor/autoload.php';

/**
 * Description : $send_file : (TRUE - will be sent, FALSE - will not be sent), $file_to_be_sent : Raw text content to convert as document file, $save_file - Save file in the folder (Path of the folder).
 * @author Anil Bind <anilkumarbind06@gmail.com>
 * @param  NULL
 * @return NULL
 */
class Emailer 
{
    public function __construct() 
    {
        /* Extra info as per project */
        if(!SEND_EMAIL)
        {
            return TRUE;
        }
        /* Extra info as per project */

        $this->smtp_user        = SMTP_USERNAME; /* Email id of the user */
        $this->smtp_password    = SMTP_PASSWORD; /* Password of the user */
        
        $this->mail = new PHPMailer(true);
    }

    public function mail($from, $to, $subject, $content, $attachment = NULL, $cc = NULL, $send_file = FALSE, $file_to_be_sent = FALSE, $save_file = FALSE, $bcc = NULL, $simple_content = NULL)
    {
        if(strtolower($send_file) == 'pdf')
        {
            $file_data = $this->pdf($file_to_be_sent, $save_file, $content, $attachment);
            $attachment = $file_data['attachfile'];
        }

        $this->mail->IsMAIL();

        //Set who the message is to be sent from
        $this->mail->setFrom($from['email'], $from['name']);
        //Set an alternative reply-to address
        /*$this->mail->addReplyTo($replyto,$replytoname);*/
        //Set who the message is to be sent to
        /*$this->mail->addAddress($sentto,$senttoname);*/
        if(is_array($to)){ foreach ($to as $key => $value) { $this->mail->addAddress(trim($value),trim($key)); } }

        if(is_array($bcc)){ foreach ($bcc as $key => $value) { $this->mail->addBCC(trim($value),trim($key)); } }

        if(is_array($cc)){ foreach ($cc as $key => $value) { $this->mail->addCC(trim($value),trim($key)); } }
        //Set the subject line
        $this->mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,

        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML($content, dirname(__FILE__));

        //Replace the plain text body with one created manually
        $this->mail->AltBody = $simple_content;
        
        //Attach an image file
        if(!empty($attachment))
        { 
            if(is_array($attachment)){ foreach ($attachment as $key => $value) { $this->mail->addAttachment($value); } } else { $this->mail->addAttachment($attachment); }
        }
        //Send the message, check for errors
        /*$sent = (!$this->mail->send()) ? "Mailer Error: " . $this->mail->ErrorInfo : TRUE;*/
        $sent = (!$this->mail->send()) ? FALSE : TRUE;

        if($send_file && !$save_file)
        {
            unlink($file_data['filenamepath']);
        }
        else if($send_file && $save_file)
        {
            $response['file_name'] = $file_data['pdffilename'];
        }

        $response['success'] = $sent;

        return $response;
    }

    public function smtp($from, $to, $subject, $content, $attachment = NULL, $cc = NULL, $send_file = FALSE, $file_to_be_sent = FALSE, $save_file = FALSE, $bcc = NULL, $simple_content = NULL)
    {
        if($send_file)
        {
            $file_data = $this->pdf($file_to_be_sent, $save_file, $content, $attachment);
            $attachment = $file_data['attachfile'];
        }

        $this->mail->isSMTP();

        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        $this->mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = $this->smtp_user;
        //Password to use for SMTP authentication
        $this->mail->Password = $this->smtp_password;
        //Set who the message is to be sent from
        $this->mail->setFrom($from['email'], $from['name']);
        //Set an alternative reply-to address
        /*$this->mail->addReplyTo('anil@hepta.me', 'First Last');*/
        //Set who the message is to be sent to
        /*$this->mail->addAddress($sentto, $senttoname);*/
        if(is_array($to) && !empty($to)){ foreach ($to as $key => $value) { $this->mail->addAddress(trim($value),trim($key)); } }

        if(is_array($bcc) && !empty($bcc)){ foreach ($bcc as $key => $value) { $this->mail->addBCC(trim($value),trim($key)); } }

        if(is_array($cc) && !empty($cc)){ foreach ($cc as $key => $value) { $this->mail->addCC(trim($value),trim($key)); } }
        //Set the subject line
        $this->mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML($content, dirname(__FILE__));
        //Replace the plain text body with one created manually
        $this->mail->AltBody = $simple_content;
        //Attach an image file
        if(!empty($attachment))
        { 
            if(is_array($attachment)) { foreach ($attachment as $key => $value) { $this->mail->addAttachment($value); } } else { $this->mail->addAttachment($attachment); }
        }else{
            //$this->mail->addAttachment('');
        }

        //Send the message, check for errors
        /*$sent = (!$this->mail->send()) ? "Mailer Error: " . $this->mail->ErrorInfo : TRUE;*/
        $sent = (!$this->mail->send()) ? FALSE : TRUE;

        if($send_file && !$save_file)
        {
            unlink($file_data['filenamepath']);
        }
        else if($send_file && $save_file)
        {
            $response['file_name'] = $file_data['pdffilename'];
        }

        $response['success'] = $sent;

        return $response;
    }

    public function pdf($file_to_be_sent, $save_file, $content, $attachment)
    {
        $pdffile = ($file_to_be_sent) ? $file_to_be_sent : $content;

        /* Start To Generate PDF */
        /*$content = file_get_contents($pdffile); $dompdf->load_html($content); If file is .html*/

        $this->dompdf = new DOMPDF();
        $this->dompdf->load_html($pdffile);
        $this->dompdf->render();
        $output = $this->dompdf->output();

        $savepdf = ($save_file) ? $save_file : 'pdf_temp';

        $pdffilename = get_rand_alphanumeric().'.pdf';
        
        $dir = trailing_slash($savepdf);
        if(!file_exists($dir) && !is_dir($dir)) 
        {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);  
        } 

        $filenamepath = trailing_slash($savepdf).$pdffilename;

        file_put_contents($filenamepath, $output);
        /* End To Generate PDF */

        if(!empty($attachment))
        { 
            if(is_array($attachment))
            {
                array_push($attachment,$filenamepath);
                $attachfile = $attachment;
            }
            else
            {
                $attachfile = array($attachment,$filenamepath);
            }
        }
        else
        {
            $attachfile = $filenamepath;
        }

        $return_array = array(
            'pdffilename'   => $pdffilename,
            'filenamepath'  => $filenamepath,
            'attachfile'    => $attachfile
        );

        return $return_array;
    }

    public function download_voucher($pdffile, $save_file = ''){
        $this->dompdf = new DOMPDF();
        $this->dompdf->load_html($pdffile);
        $this->dompdf->render();
        $output = $this->dompdf->output();

        $savepdf = !empty($save_file) ? $save_file : 'pdf_temp';

        $pdffilename = get_rand_alphanumeric().'.pdf';
        
        $dir = trailing_slash($savepdf);
        if(!file_exists($dir) && !is_dir($dir)) 
        {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);  
        } 

        $filenamepath = trailing_slash($savepdf).$pdffilename;

        file_put_contents($filenamepath, $output);

        return $filenamepath;
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }
}
?>