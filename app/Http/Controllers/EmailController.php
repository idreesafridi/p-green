<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstructionSite;
use App\Models\MaterialsAsisstance;
use App\Notifications\EmailReminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Notification;

class EmailController extends Controller
{
    private $_request = null;
    private $_directory = 'emails.';
    // private $_from = 'greengen@crm-crisaloid.com';
    private $_from = 'greengen@crm-labloid.com';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Sending reminder emails
     */
    public function reminder_emails()
    {
        $toMail = $this->_request->to_mail;
        //dd($this->_request->all());
        $folder = $this->_request->folder;
        $conId = $this->_request->conId;

        $conData = ConstructionSite::where('id', $conId)->select('name', 'surename')->first();

        if ($conData != null) {
            $subject = "!! Documento MANCANTE " . $folder . " | Cantiere " . $conData->name . " " . $conData->surename;
            $path = $this->_directory . "mail-mancanza-doc-generale";

            Mail::send($path, [], function ($message) use ($toMail, $subject) {
                $message->to($toMail)
                    ->subject($subject);
            });

            //Notification::route('mail', $toMail)->notify((new EmailReminder($subject, $path)));

            return redirect()->back()->with('success', 'Invio e-mail di promemoria');
        } else {
            return redirect()->back()->with('error', 'Controlla di nuovo la tua richiesta');
        }
    }
    // reminder_emails_assistensi
    public function reminder_emails_assistensi()
    {
        $all_assistensi = MaterialsAsisstance::where('expiry_date', '!=', null)->get();
        // Loop through the assistences and send reminders if needed
        foreach ($all_assistensi as $assistance) {

            $expiry_date = Carbon::parse($assistance->expiry_date);

            // Define the reminder dates
            $reminder_date_7_days = Carbon::now()->addDays(7);
            $reminder_date_2_days = Carbon::now()->addDays(2);

            // Get the construction site for this assistance
            $construction_site = ConstructionSite::where('id', $assistance->construction_site_id)->first();



            if (!$construction_site) {
                continue; // Skip this assistance if the construction site is not found
            }
            $centre_name = $construction_site->name . '' . $construction_site->surename;

            $email = 'ASSISENTENZE.GREENGEN@GMAIL.COM';

            // Send reminder email 7 days before the expiry date
            if ($expiry_date->eq($reminder_date_7_days)) {
                Mail::send('emails.reminder_assistences', ['cantiere_name' => $centre_name], function ($message) use ($assistance, $email) {
                    $message->to($email);
                    $message->subject('-7 GG ASSISTENZA ' . $centre_name);
                });
            }

            // Send reminder email 2 days before the expiry date
            if ($expiry_date->eq($reminder_date_2_days)) {
                Mail::send('emails.reminder_assistences', ['cantiere_name' => $centre_name], function ($message) use ($assistance, $email) {
                    $message->to($email);
                    $message->subject('-2 GG ASSISTENZA ' . $centre_name);
                });
            }
        }

        return redirect()->back()->with('success', 'E-mail di promemoria inviata');
    }



}
