<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenerateOfferLetter extends Model
{
    protected $table = 'generate_offer_letters';
    protected $fillable = [
        'id',
        'lang',
        'content',
        'created_by',
    ];



    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{applicant_name}',
            '{app_name}',
            '{job_title}',
            '{job_type}',
            '{start_date}',
            '{workplace_location}',
            '{days_of_week}',
            '{salary}',
            '{salary_type}',
            '{salary_duration}',
            '{next_pay_period}',
            '{offer_expiration_date}',


        ];
        $arrValue    = [
            'applicant_name' => '-',
            'app_name' => '-',
            'job_title' => '-',
            'job_type' => '-',
            'start_date' => '-',
            'workplace_location' => '-',
            'days_of_week' => '-',
            'salary'=>'-',
            'salary_type' => '-',
            'salary_duration' => '-',
            'next_pay_period' => '-',
            'offer_expiration_date' => '-',

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

    public static function defaultOfferLetter()
    {

        $defaultTemplate = [

                    'ar' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>رسالة عرض</strong></span></p>


                    <p>عزيزي {applicationant_name} ،</p>


                    <p>{app_name} متحمس لاصطحابك على متن الطائرة بصفتك {job_title}.</p>

                    <p>كنت على بعد خطوات قليلة من الشروع في العمل. يرجى أخذ الوقت الكافي لمراجعة عرضنا الرسمي. يتضمن تفاصيل مهمة حول راتبك ومزاياك وبنود وشروط عملك المتوقع مع {app_name}.</p>


                    <p>{app_name} يقدم {job_type}. المنصب بالنسبة لك كـ {job_title} ، تقديم التقارير إلى [المدير المباشر / المشرف] بدءًا من {start_date} في {workplace_location}. ساعات العمل المتوقعة هي {days_of_week}.</p>


                    <p>في هذا المنصب ، يعرض عليك {app_name}&nbsp; {salary}أن تبدأ لك بمعدل دفع {salary_type} لكل {salary_duration}. سوف يتم الدفع لك على أساس.</p>


                    <p>كجزء من تعويضك ، إذا كان ذلك ممكنًا ، ستصف مكافأتك ومشاركة الأرباح وهيكل العمولة وخيارات الأسهم وقواعد لجنة التعويضات هنا.</p>


                    <p>بصفتك موظفًا في {app_name} ، ستكون مؤهلاً للحصول على مزايا الاسم المختصر ، مثل التأمين الصحي ، وخطة الأسهم ، والتأمين على الأسنان ، وما إلى ذلك.</p>


                    <p>الرجاء توضيح موافقتك على هذه البنود وقبول هذا العرض عن طريق التوقيع على هذه الاتفاقية وتأريخها في أو قبل {offer_expiration_date}.</p>

                    <p>بإخلاص،</p>

                    <p>{app_name}</p>',


                    'en' => '<p style="text-align: center;"><strong>Offer Letter</strong></p>

                    <p>Dear {applicant_name},</p>

                    <p>{app_name} is excited to bring you on board as {job_title}.</p>

                    <p>Were just a few formalities away from getting down to work. Please take the time to review our formal offer. It includes important details about your compensation, benefits, and the terms and conditions of your anticipated employment with {app_name}.</p>

                    <p>{app_name} is offering a {job_type}. position for you as {job_title}, reporting to [immediate manager/supervisor] starting on {start_date} at{workplace_location}. Expected hours of work are{days_of_week}.</p>

                    <p>In this position, {app_name} is offering to start you at a pay rate of {salary} per {salary_type}. You will be paid on a{salary_duration} basis.&nbsp;</p>

                    <p>As part of your compensation, were also offering [if applicable, youll describe your bonus, profit sharing, commission structure, stock options, and compensation committee rules here].</p>

                    <p>As an employee of {app_name} , you will be eligible for briefly name benefits, such as health insurance, stock plan, dental insurance, etc.</p>

                    <p>Please indicate your agreement with these terms and accept this offer by signing and dating this agreement on or before {offer_expiration_date}.</p>

                    <p>Sincerely,</p>

                    <p>{app_name}</p>',

                    'fr' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>Lettre doffre</strong></span></p>


                    <p>Cher {applicant_name},</p>


                    <p>{app_name} est ravi de vous accueillir en tant que {job_title}.</p>


                    <p>&Eacute;taient juste quelques formalit&eacute;s loin de se mettre au travail. Veuillez prendre le temps dexaminer notre offre formelle. Il comprend des d&eacute;tails importants sur votre r&eacute;mun&eacute;ration, vos avantages et les termes et conditions de votre emploi pr&eacute;vu avec {app_name}.</p>


                    <p>{app_name} propose un {job_type}. poste pour vous en tant que {job_title}, relevant du directeur/superviseur imm&eacute;diat &agrave; partir du {start_date} &agrave; {workplace_location}. Les heures de travail pr&eacute;vues sont de {days_of_week}.</p>


                    <p>&Agrave; ce poste, {app_name} vous propose de commencer avec un taux de r&eacute;mun&eacute;ration de {salary} par {salary_type}. Vous serez pay&eacute; sur une base de {salary_duration}.</p>


                    <p>Dans le cadre de votre r&eacute;mun&eacute;ration, le cas &eacute;ch&eacute;ant, vous d&eacute;crivez ici votre bonus, votre participation aux b&eacute;n&eacute;fices, votre structure de commission, vos options sur actions et les r&egrave;gles du comit&eacute; de r&eacute;mun&eacute;ration.</p>


                    <p>En tant quemploy&eacute; de {app_name}, vous aurez droit &agrave; des avantages bri&egrave;vement nomm&eacute;s, tels que lassurance maladie, le plan dactionnariat, lassurance dentaire, etc.</p>


                    <p>Veuillez indiquer votre accord avec ces conditions et accepter cette offre en signant et en datant cet accord au plus tard le {offer_expiration_date}.</p>


                    <p>Sinc&egrave;rement,</p>

                    <p>{app_name}</p>',

        ];


        foreach($defaultTemplate as $lang => $content)
        {
            GenerateOfferLetter::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => 2,

                ]
            );
        }

    }
    public static function defaultOfferLetterRegister($user_id)
    {

        $defaultTemplate = [

                    'ar' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>رسالة عرض</strong></span></p>


                    <p>عزيزي {applicationant_name} ،</p>


                    <p>{app_name} متحمس لاصطحابك على متن الطائرة بصفتك {job_title}.</p>

                    <p>كنت على بعد خطوات قليلة من الشروع في العمل. يرجى أخذ الوقت الكافي لمراجعة عرضنا الرسمي. يتضمن تفاصيل مهمة حول راتبك ومزاياك وبنود وشروط عملك المتوقع مع {app_name}.</p>


                    <p>{app_name} يقدم {job_type}. المنصب بالنسبة لك كـ {job_title} ، تقديم التقارير إلى [المدير المباشر / المشرف] بدءًا من {start_date} في {workplace_location}. ساعات العمل المتوقعة هي {days_of_week}.</p>


                    <p>في هذا المنصب ، يعرض عليك {app_name}&nbsp; {salary}أن تبدأ لك بمعدل دفع {salary_type} لكل {salary_duration}. سوف يتم الدفع لك على أساس.</p>


                    <p>كجزء من تعويضك ، إذا كان ذلك ممكنًا ، ستصف مكافأتك ومشاركة الأرباح وهيكل العمولة وخيارات الأسهم وقواعد لجنة التعويضات هنا.</p>


                    <p>بصفتك موظفًا في {app_name} ، ستكون مؤهلاً للحصول على مزايا الاسم المختصر ، مثل التأمين الصحي ، وخطة الأسهم ، والتأمين على الأسنان ، وما إلى ذلك.</p>


                    <p>الرجاء توضيح موافقتك على هذه البنود وقبول هذا العرض عن طريق التوقيع على هذه الاتفاقية وتأريخها في أو قبل {offer_expiration_date}.</p>

                    <p>بإخلاص،</p>

                    <p>{app_name}</p>',

                    'en' => '<p style="text-align: center;"><strong>Offer Letter</strong></p>

                    <p>Dear {applicant_name},</p>

                    <p>{app_name} is excited to bring you on board as {job_title}.</p>

                    <p>Were just a few formalities away from getting down to work. Please take the time to review our formal offer. It includes important details about your compensation, benefits, and the terms and conditions of your anticipated employment with {app_name}.</p>

                    <p>{app_name} is offering a {job_type}. position for you as {job_title}, reporting to [immediate manager/supervisor] starting on {start_date} at{workplace_location}. Expected hours of work are{days_of_week}.</p>

                    <p>In this position, {app_name} is offering to start you at a pay rate of {salary} per {salary_type}. You will be paid on a{salary_duration} basis.&nbsp;</p>

                    <p>As part of your compensation, were also offering [if applicable, youll describe your bonus, profit sharing, commission structure, stock options, and compensation committee rules here].</p>

                    <p>As an employee of {app_name} , you will be eligible for briefly name benefits, such as health insurance, stock plan, dental insurance, etc.</p>

                    <p>Please indicate your agreement with these terms and accept this offer by signing and dating this agreement on or before {offer_expiration_date}.</p>

                    <p>Sincerely,</p>
                    <p>{app_name}</p>',
                    'es' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>Carta de oferta</strong></span></p>


                    <p>Estimado {applicant_name},</p>

                    <p>{app_name} se complace en incorporarlo como {job_title}.</p>


                    <p>Faltaban s&oacute;lo unos tr&aacute;mites para ponerse manos a la obra. T&oacute;mese el tiempo para revisar nuestra oferta formal. Incluye detalles importantes sobre su compensaci&oacute;n, beneficios y los t&eacute;rminos y condiciones de su empleo anticipado con {app_name}.</p>


                    <p>{app_name} est&aacute; ofreciendo {job_type}. posici&oacute;n para usted como {job_title}, reportando al gerente/supervisor inmediato a partir del {start_date} en {workplace_location}. Las horas de trabajo esperadas son {days_of_week}.</p>


                    <p>En este puesto, {app_name} te ofrece comenzar con una tarifa de pago de {salary} por {salary_type}. Se le pagar&aacute; sobre la base de {salary_duration}.</p>


                    <p>Como parte de su compensaci&oacute;n, tambi&eacute;n ofrecemos, si corresponde, aqu&iacute; describir&aacute; su bonificaci&oacute;n, participaci&oacute;n en las ganancias, estructura de comisiones, opciones sobre acciones y reglas del comit&eacute; de compensaci&oacute;n.</p>


                    <p>Como empleado de {app_name}, ser&aacute; elegible para beneficios de nombre breve, como seguro m&eacute;dico, plan de acciones, seguro dental, etc.</p>


                    <p>Indique su acuerdo con estos t&eacute;rminos y acepte esta oferta firmando y fechando este acuerdo el {offer_expiration_date} o antes.</p>


                    <p>Sinceramente,</p>

                    <p>{app_name}</p>',

                    'fr' => '<p style="text-align: center;"><span style="font-size: 18pt;"><strong>Lettre doffre</strong></span></p>


                    <p>Cher {applicant_name},</p>


                    <p>{app_name} est ravi de vous accueillir en tant que {job_title}.</p>


                    <p>&Eacute;taient juste quelques formalit&eacute;s loin de se mettre au travail. Veuillez prendre le temps dexaminer notre offre formelle. Il comprend des d&eacute;tails importants sur votre r&eacute;mun&eacute;ration, vos avantages et les termes et conditions de votre emploi pr&eacute;vu avec {app_name}.</p>


                    <p>{app_name} propose un {job_type}. poste pour vous en tant que {job_title}, relevant du directeur/superviseur imm&eacute;diat &agrave; partir du {start_date} &agrave; {workplace_location}. Les heures de travail pr&eacute;vues sont de {days_of_week}.</p>


                    <p>&Agrave; ce poste, {app_name} vous propose de commencer avec un taux de r&eacute;mun&eacute;ration de {salary} par {salary_type}. Vous serez pay&eacute; sur une base de {salary_duration}.</p>


                    <p>Dans le cadre de votre r&eacute;mun&eacute;ration, le cas &eacute;ch&eacute;ant, vous d&eacute;crivez ici votre bonus, votre participation aux b&eacute;n&eacute;fices, votre structure de commission, vos options sur actions et les r&egrave;gles du comit&eacute; de r&eacute;mun&eacute;ration.</p>


                    <p>En tant quemploy&eacute; de {app_name}, vous aurez droit &agrave; des avantages bri&egrave;vement nomm&eacute;s, tels que lassurance maladie, le plan dactionnariat, lassurance dentaire, etc.</p>


                    <p>Veuillez indiquer votre accord avec ces conditions et accepter cette offre en signant et en datant cet accord au plus tard le {offer_expiration_date}.</p>


                    <p>Sinc&egrave;rement,</p>
                    <p>{app_name}</p>',


        ];


        foreach($defaultTemplate as $lang => $content)
        {
            GenerateOfferLetter::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => $user_id,

                ]
            );
        }

    }


}
