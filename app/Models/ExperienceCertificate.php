<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceCertificate extends Model
{
    protected $table = 'experience_certificates';
    protected $fillable = [
        'id',
        'lang',
        'content',
        'created_by'
    ];



    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{app_name}',
            '{date}',
            '{employee_name}',
            '{duration}',
            '{designation}',
            '{payroll}',



        ];
        $arrValue    = [
            'app_name' => '-',
            'date' => '-',
            'employee_name' => '-',
            'duration' => '-',
            'designation' => '-',
            'payroll' => '-',


        ];

        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }
        $settings = Utility::settings();

        //   dd(env('APP_NAME'));
        $arrValue['app_name']     = env('APP_NAME');


        return str_replace($arrVariable, array_values($arrValue), $content);
    }
    public static function defaultExpCertificat()
    {


        $defaultTemplate = [

            'ar' => '<h3 style="text-align: center;">بريد إلكتروني تجربة</h3>


            <p>{app_name}</p>

            <p>إلي من يهمه الامر</p>

            <p>{date}</p>

            <p>{employee_name}</p>

            <p>مدة الخدمة {duration} في {app_name}.</p>

            <p>{designation}</p>

            <p>{payroll}</p>

            <p>الادوار والمسؤوليات</p>

            <p>وصف موجز لمسار عمل الموظف وبيان إيجابي من المدير أو المشرف.</p>

            <p>بإخلاص،</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>التوقيع</p>

            <p>{app_name}</p>',


            'en' => '<p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: center;" align="center"><span style="font-size: 18pt;"><strong>Experience Letter</strong></span></p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">&nbsp;</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{app_name}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">TO WHOM IT MAY CONCERN</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{date}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{employee_name}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Tenure of Service {duration} in {app_name}.</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{designation}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{payroll}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Roles and Responsibilities</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">&nbsp;</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Brief description of the employee&rsquo;s course of employment and a positive statement from the manager or supervisor.</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">&nbsp;</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Sincerely,</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{employee_name}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{designation}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Signature</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{app_name}</p>',

            'fr' => '<h3 style="text-align: center;">Lettre dexp&eacute;rience</h3>



            <p>{app_name}</p>

            <p>&Agrave; QUI DE DROIT</p>

            <p>{date}</p>

            <p>{employee_name}</p>

            <p>Dur&eacute;e du service {duration} dans {app_name}.</p>

            <p>{designation}</p>

            <p>{payroll}</p>

            <p>R&ocirc;les et responsabilit&eacute;s</p>



            <p>Br&egrave;ve description de l&eacute;volution de lemploi de lemploy&eacute; et une d&eacute;claration positive du gestionnaire ou du superviseur.</p>



            <p>Sinc&egrave;rement,</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>Signature</p>

            <p>{app_name}</p>',


     ];


        foreach($defaultTemplate as $lang => $content)
        {
            ExperienceCertificate::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => 2,

                ]
            );
        }

    }
    public static function defaultExpCertificatRegister($user_id)
    {


        $defaultTemplate = [

            'ar' => '<h3 style="text-align: center;">بريد إلكتروني تجربة</h3>



            <p>{app_name}</p>

            <p>إلي من يهمه الامر</p>

            <p>{date}</p>

            <p>{employee_name}</p>

            <p>مدة الخدمة {duration} في {app_name}.</p>

            <p>{designation}</p>

            <p>{payroll}</p>

            <p>الادوار والمسؤوليات</p>



            <p>وصف موجز لمسار عمل الموظف وبيان إيجابي من المدير أو المشرف.</p>



            <p>بإخلاص،</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>التوقيع</p>

            <p>{app_name}</p>',

            'en' => '<p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: center;" align="center"><span style="font-size: 18pt;"><strong>Experience Letter</strong></span></p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">&nbsp;</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{app_name}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">TO WHOM IT MAY CONCERN</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{date}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{employee_name}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Tenure of Service {duration} in {app_name}.</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{designation}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{payroll}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Roles and Responsibilities</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">&nbsp;</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Brief description of the employee&rsquo;s course of employment and a positive statement from the manager or supervisor.</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">&nbsp;</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Sincerely,</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{employee_name}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{designation}</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">Signature</p>
            <p lang="en-IN" style="margin-bottom: 0cm; direction: ltr; line-height: 2; text-align: left;" align="center">{app_name}</p>',
            'fr' => '<h3 style="text-align: center;">Lettre dexp&eacute;rience</h3>



            <p>{app_name}</p>

            <p>&Agrave; QUI DE DROIT</p>

            <p>{date}</p>

            <p>{employee_name}</p>

            <p>Dur&eacute;e du service {duration} dans {app_name}.</p>

            <p>{designation}</p>

            <p>{payroll}</p>

            <p>R&ocirc;les et responsabilit&eacute;s</p>



            <p>Br&egrave;ve description de l&eacute;volution de lemploi de lemploy&eacute; et une d&eacute;claration positive du gestionnaire ou du superviseur.</p>



            <p>Sinc&egrave;rement,</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>Signature</p>

            <p>{app_name}</p>',

     ];


        foreach($defaultTemplate as $lang => $content)
        {
            ExperienceCertificate::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => $user_id,

                ]
            );
        }

    }
}
