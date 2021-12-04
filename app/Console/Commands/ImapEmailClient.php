<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Appointment;
use Carbon\Carbon;
use DB;
use Mail;
use Date;

class ImapEmailClient extends Command
{
    protected $signature = 'fetch:imap_email';

    protected $description = 'fetch:imap_email';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
		$email_data = [];

		$mailbox = new \PhpImap\Mailbox(
			'{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX', // IMAP server and mailbox folder
			'cadmanedwards1000@gmail.com', // Username for the before configured mailbox
			'1@Ab56ab56', // Password for the before configured username
			__DIR__, // Directory, where attachments will be saved (optional)
			$serverEncoding = 'UTF-8' // Server encoding (optional)
		);
		$mailbox->setPathDelimiter('/');
		$mailbox->setAttachmentsDir(public_path('/'));

		try {
			$mailsIds = $mailbox->searchMailbox('UNSEEN');
		} catch(\PhpImap\Exceptions\ConnectionException $ex) {
			echo "IMAP connection failed: " . $ex;
			die();
		}

		foreach($mailsIds as $mail_id) {
            $items=[];
            $email = $mailbox->getMail(
              $mail_id, // ID of the email, you want to get
              false // Do NOT mark emails as seen
            );

            if (!$mailbox->getAttachmentsIgnore()) {
                $attachments = $email->getAttachments();
                $filename=[];
                foreach ($attachments as $attachment) {

					$filename["name"]=$attachment->name;
                    $filename["path"]=$attachment->filePath;
                    $source =  $filename["path"]; 
                    $destination = public_path("/email_attachments/".$filename["name"]); 
                    if( copy($source, $destination) ) { 
                        unlink($source);
                    } 
					\DB::table('imap_attachments')->insert([
						'attachment' => $filename["name"]
					]);
                    $items["filename"] = $filename["name"];
                }
                $email_data[] = $items;
            }
          }
          $arr = array_reverse($email_data);

		  echo json_encode($arr);
		  $mailbox->disconnect();
 

	}
}