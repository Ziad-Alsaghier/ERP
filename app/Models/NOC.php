<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NOC extends Model
{
    protected $table = 'noc_certificates';
    protected $fillable = [
        'id',
        'lang',
        'content',
        'created_by',
    ];



    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{date}',
            '{employee_name}',
            '{designation}',
            '{app_name}',

        ];
        $arrValue    = [
            'date' => '-',
            'employee_name' => '-',
            'designation' => '-',
            'app_name' => '-',
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
    public static function defaultNocCertificate()
    {


        $defaultTemplate = [

            'ar' => '<h3 style="text-align: center;">شهادة عدم ممانعة</h3>



            <p>التاريخ: {date}</p>



            <p>إلى من يهمه الأمر</p>



            <p>هذه الشهادة مخصصة للمطالبة بشهادة عدم ممانعة (NoC) للسيدة / السيد {employee_name} إذا انضمت إلى أي مؤسسة أخرى وقدمت خدماتها / خدماتها. يتم إبلاغه لأنه قام بتصفية جميع أرصدته واستلام أمانه من شركة {app_name}.</p>



            <p>نتمنى لها / لها التوفيق في المستقبل.</p>



            <p>بإخلاص،</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>التوقيع</p>

            <p>{app_name}</p>',

            'en' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>No Objection Certificate</strong></span></p>

            <p>Date: {date}</p>

            <p>To Whomsoever It May Concern</p>

            <p>This certificate is to claim a No Objection Certificate (NoC) for Ms. / Mr. {employee_name} if she/he joins and provides her/his services to any other organization. It is informed as she/he has cleared all her/his balances and received her/his security from {app_name} Company.</p>

            <p>We wish her/him good luck in the future.</p>

            <p>Sincerely,</p>
            <p>{employee_name}</p>
            <p>{designation}</p>
            <p>Signature</p>
            <p>{app_name}</p>',

            'es' => '<h3 style="text-align: center;">Certificado de conformidad</h3>



            <p>Fecha: {date}</p>



            <p>A quien corresponda</p>



            <p>Este certificado es para reclamar un Certificado de No Objeci&oacute;n (NoC) para la Sra. / Sr. {employee_name} si ella / &eacute;l se une y brinda sus servicios a cualquier otra organizaci&oacute;n. Se informa que &eacute;l/ella ha liquidado todos sus saldos y recibido su seguridad de {app_name} Company.</p>



            <p>Le deseamos buena suerte en el futuro.</p>



            <p>Sinceramente,</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>Firma</p>

            <p>{app_name}</p>',

            'fr' => '<h3 style="text-align: center;">Aucun certificat dopposition</h3>


            <p>Date : {date}</p>


            <p>&Agrave; toute personne concern&eacute;e</p>


            <p>Ce certificat sert &agrave; r&eacute;clamer un certificat de non-objection (NoC) pour Mme / M. {employee_name} sil rejoint et fournit ses services &agrave; toute autre organisation. Il est inform&eacute; quil a sold&eacute; tous ses soldes et re&ccedil;u sa garantie de la part de la soci&eacute;t&eacute; {app_name}.</p>


            <p>Nous lui souhaitons bonne chance pour lavenir.</p>


            <p>Sinc&egrave;rement,</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>Signature</p>

            <p>{app_name}</p>',

       ];


        foreach($defaultTemplate as $lang => $content)
        {
            NOC::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => 2,

                ]
            );
        }

    }
    public static function defaultNocCertificateRegister($user_id)
    {


        $defaultTemplate = [

            'ar' => '<h3 style="text-align: center;">شهادة عدم ممانعة</h3>



            <p>التاريخ: {date}</p>



            <p>إلى من يهمه الأمر</p>



            <p>هذه الشهادة مخصصة للمطالبة بشهادة عدم ممانعة (NoC) للسيدة / السيد {employee_name} إذا انضمت إلى أي مؤسسة أخرى وقدمت خدماتها / خدماتها. يتم إبلاغه لأنه قام بتصفية جميع أرصدته واستلام أمانه من شركة {app_name}.</p>



            <p>نتمنى لها / لها التوفيق في المستقبل.</p>



            <p>بإخلاص،</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>التوقيع</p>

            <p>{app_name}</p>',

            'en' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>No Objection Certificate</strong></span></p>

            <p>Date: {date}</p>

            <p>To Whomsoever It May Concern</p>

            <p>This certificate is to claim a No Objection Certificate (NoC) for Ms. / Mr. {employee_name} if she/he joins and provides her/his services to any other organization. It is informed as she/he has cleared all her/his balances and received her/his security from {app_name} Company.</p>

            <p>We wish her/him good luck in the future.</p>

            <p>Sincerely,</p>
            <p>{employee_name}</p>
            <p>{designation}</p>
            <p>Signature</p>
            <p>{app_name}</p>',
            'es' => '<h3 style="text-align: center;">Certificado de conformidad</h3>



            <p>Fecha: {date}</p>



            <p>A quien corresponda</p>



            <p>Este certificado es para reclamar un Certificado de No Objeci&oacute;n (NoC) para la Sra. / Sr. {employee_name} si ella / &eacute;l se une y brinda sus servicios a cualquier otra organizaci&oacute;n. Se informa que &eacute;l/ella ha liquidado todos sus saldos y recibido su seguridad de {app_name} Company.</p>



            <p>Le deseamos buena suerte en el futuro.</p>



            <p>Sinceramente,</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>Firma</p>

            <p>{app_name}</p>',

            'fr' => '<h3 style="text-align: center;">Aucun certificat dopposition</h3>


            <p>Date : {date}</p>


            <p>&Agrave; toute personne concern&eacute;e</p>


            <p>Ce certificat sert &agrave; r&eacute;clamer un certificat de non-objection (NoC) pour Mme / M. {employee_name} sil rejoint et fournit ses services &agrave; toute autre organisation. Il est inform&eacute; quil a sold&eacute; tous ses soldes et re&ccedil;u sa garantie de la part de la soci&eacute;t&eacute; {app_name}.</p>


            <p>Nous lui souhaitons bonne chance pour lavenir.</p>


            <p>Sinc&egrave;rement,</p>

            <p>{employee_name}</p>

            <p>{designation}</p>

            <p>Signature</p>

            <p>{app_name}</p>',


       ];


        foreach($defaultTemplate as $lang => $content)
        {
            NOC::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => $user_id,

                ]
            );
        }

    }
}
