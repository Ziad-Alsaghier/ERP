<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class JoiningLetter extends Model
{
    protected $table = 'joining_letters';
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
            '{app_name}',
            '{employee_name}',
            '{address}',
            '{start_date}',
            '{designation}',
            '{branch}',
            '{start_time}',
            '{end_time}',
            '{total_hours}',


        ];
        $arrValue    = [
            'date' => '-',
            'app_name' => '-',
            'employee_name' => '-',
            'address' => '-',
            'start_date' => '-',
            'designation' => '-',
            'branch' => '-',
            'start_time' => '-',
            'end_time' => '-',
            'total_hours' => '-',

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
    public static function defaultJoiningLetter()
    {

        $defaultTemplate = [

            'ar' => '<h2 style="text-align: center;"><strong>خطاب الانضمام</strong></h2>
            <p>{date}</p>
            <p>{employee_name}</p>
            <p>{address}</p>
            <p>الموضوع: موعد لوظيفة {designation}</p>
            <p>عزيزي {employee_name} ،</p>
            <p>يسعدنا أن نقدم لك منصب {designation} مع {app_name} "الشركة" وفقًا للشروط التالية و</p>
            <p>الظروف:</p>
            <p>1. بدء العمل</p>
            <p>سيصبح عملك ساريًا اعتبارًا من {start_date}</p>
            <p>2. المسمى الوظيفي</p>
            <p>سيكون المسمى الوظيفي الخاص بك هو {designation}.</p>
            <p>3. الراتب</p>
            <p>سيكون راتبك والمزايا الأخرى على النحو المبين في الجدول 1 ، طيه.</p>
            <p>4. مكان الإرسال</p>
            <p>سيتم إرسالك إلى {branch}. ومع ذلك ، قد يُطلب منك العمل في أي مكان عمل تمتلكه الشركة ، أو</p>
            <p>قد تحصل لاحقًا.</p>
            <p>5. ساعات العمل</p>
            <p>أيام العمل العادية هي من الاثنين إلى الجمعة. سيُطلب منك العمل لساعات حسب الضرورة لـ</p>
            <p>أداء واجباتك على النحو الصحيح تجاه الشركة. ساعات العمل العادية من {start_time} إلى {end_time} وأنت</p>
            <p>من المتوقع أن يعمل ما لا يقل عن {total_hours} ساعة كل أسبوع ، وإذا لزم الأمر لساعات إضافية اعتمادًا على</p>
            <p>المسؤوليات.</p>
            <p>6. الإجازة / العطل</p>
            <p>6.1 يحق لك الحصول على إجازة غير رسمية مدتها 12 يومًا.</p>
            <p>6.2 يحق لك الحصول على إجازة مرضية مدفوعة الأجر لمدة 12 يوم عمل.</p>
            <p>6.3 تخطر الشركة بقائمة الإجازات المعلنة في بداية كل عام.</p>
            <p>7. طبيعة الواجبات</p>
            <p>ستقوم بأداء أفضل ما لديك من واجبات متأصلة في منصبك ومهام إضافية مثل الشركة</p>
            <p>قد يدعوك لأداء ، من وقت لآخر. واجباتك المحددة منصوص عليها في الجدول الثاني بهذه الرسالة.</p>
            <p>8. ممتلكات الشركة</p>
            <p>ستحافظ دائمًا على ممتلكات الشركة في حالة جيدة ، والتي قد يتم تكليفك بها للاستخدام الرسمي خلال فترة عملها</p>
            <p>عملك ، ويجب أن تعيد جميع هذه الممتلكات إلى الشركة قبل التخلي عن الرسوم الخاصة بك ، وإلا فإن التكلفة</p>
            <p>نفس الشيء سوف تسترده منك الشركة.</p>
            <p>9. الاقتراض / قبول الهدايا</p>
            <p>لن تقترض أو تقبل أي أموال أو هدية أو مكافأة أو تعويض مقابل مكاسبك الشخصية من أو تضع نفسك بأي طريقة أخرى</p>
            <p>بموجب التزام مالي تجاه أي شخص / عميل قد تكون لديك تعاملات رسمية معه.</p>
            <p>10. الإنهاء</p>
            <p>10.1 يمكن للشركة إنهاء موعدك ، دون أي سبب ، من خلال إعطائك ما لا يقل عن [إشعار] قبل أشهر</p>
            <p>إشعار خطي أو راتب بدلاً منه. لغرض هذا البند ، يقصد بالراتب المرتب الأساسي.</p>
            <p>10.2 إنهاء عملك مع الشركة ، دون أي سبب ، من خلال تقديم ما لا يقل عن إشعار الموظف</p>
            <p>أشهر الإخطار أو الراتب عن الفترة غير المحفوظة ، المتبقية بعد تعديل الإجازات المعلقة ، كما في التاريخ.</p>
            <p>10.3 تحتفظ الشركة بالحق في إنهاء عملك بإيجاز دون أي فترة إشعار أو مدفوعات إنهاء</p>
            <p>إذا كان لديه سبب معقول للاعتقاد بأنك مذنب بسوء السلوك أو الإهمال ، أو ارتكبت أي خرق جوهري لـ</p>
            <p>العقد ، أو تسبب في أي خسارة للشركة.</p>
            <p>10. 4 عند إنهاء عملك لأي سبب من الأسباب ، ستعيد إلى الشركة جميع ممتلكاتك ؛ المستندات و</p>
            <p>الأوراق الأصلية ونسخها ، بما في ذلك أي عينات ، وأدبيات ، وعقود ، وسجلات ، وقوائم ، ورسومات ، ومخططات ،</p>
            <p>الرسائل والملاحظات والبيانات وما شابه ذلك ؛ والمعلومات السرية التي بحوزتك أو تحت سيطرتك والمتعلقة بك</p>
            <p>التوظيف أو الشؤون التجارية للعملاء.</p>
            <p>11. المعلومات السرية</p>
            <p>11. 1 أثناء عملك في الشركة ، سوف تكرس وقتك واهتمامك ومهارتك كلها بأفضل ما لديك من قدرات</p>
            <p>عملها. لا يجوز لك ، بشكل مباشر أو غير مباشر ، الانخراط أو الارتباط بنفسك ، أو الارتباط به ، أو القلق ، أو التوظيف ، أو</p>
            <p>الوقت أو متابعة أي دورة دراسية على الإطلاق ، دون الحصول على إذن مسبق من الشركة أو الانخراط في أي عمل آخر أو</p>
            <p>الأنشطة أو أي وظيفة أخرى أو العمل بدوام جزئي أو متابعة أي دورة دراسية على الإطلاق ، دون إذن مسبق من</p>
            <p>شركة.</p>
            <p>11. المعلومات السرية</p>
            <p>11. 1 أثناء عملك في الشركة ، سوف تكرس وقتك واهتمامك ومهارتك كلها بأفضل ما لديك من قدرات</p>
            <p>عملها. لا يجوز لك ، بشكل مباشر أو غير مباشر ، الانخراط أو الارتباط بنفسك ، أو الارتباط به ، أو القلق ، أو التوظيف ، أو</p>
            <p>الوقت أو متابعة أي دورة دراسية على الإطلاق ، دون الحصول على إذن مسبق من الشركة أو الانخراط في أي عمل آخر أو</p>
            <p>الأنشطة أو أي وظيفة أخرى أو العمل بدوام جزئي أو متابعة أي دورة دراسية على الإطلاق ، دون إذن مسبق من</p>
            <p>شركة.</p>
            <p>11.2 يجب عليك دائمًا الحفاظ على أعلى درجة من السرية والحفاظ على سرية السجلات والوثائق وغيرها</p>
            <p>المعلومات السرية المتعلقة بأعمال الشركة والتي قد تكون معروفة لك أو مخولة لك بأي وسيلة</p>
            <p>ولن تستخدم هذه السجلات والمستندات والمعلومات إلا بالطريقة المصرح بها حسب الأصول لصالح الشركة. إلى عن على</p>
            <p>أغراض هذا البند "المعلومات السرية" تعني المعلومات المتعلقة بأعمال الشركة وعملائها</p>
            <p>التي لا تتوفر لعامة الناس والتي قد تتعلمها أثناء عملك. هذا يشمل،</p>
            <p>على سبيل المثال لا الحصر ، المعلومات المتعلقة بالمنظمة وقوائم العملاء وسياسات التوظيف والموظفين والمعلومات</p>
            <p>حول منتجات الشركة وعملياتها بما في ذلك الأفكار والمفاهيم والإسقاطات والتكنولوجيا والكتيبات والرسم والتصاميم ،</p>
            <p>المواصفات وجميع الأوراق والسير الذاتية والسجلات والمستندات الأخرى التي تحتوي على هذه المعلومات السرية.</p>
            <p>11.3 لن تقوم في أي وقت بإزالة أي معلومات سرية من المكتب دون إذن.</p>

            <p>11.4 واجبك في الحماية وعدم الإفشاء</p>

            <p>تظل المعلومات السرية سارية بعد انتهاء أو إنهاء هذه الاتفاقية و / أو عملك مع الشركة.</p>

            <p>11.5 سوف يجعلك خرق شروط هذا البند عرضة للفصل بإجراءات موجزة بموجب الفقرة أعلاه بالإضافة إلى أي</p>

            <p>أي تعويض آخر قد يكون للشركة ضدك في القانون.</p>

            <p>12. الإخطارات</p>

            <p>يجوز لك إرسال إخطارات إلى الشركة على عنوان مكتبها المسجل. يمكن أن ترسل لك الشركة إشعارات على</p>

            <p>العنوان الذي أشرت إليه في السجلات الرسمية.</p>



            <p>13. تطبيق سياسة الشركة</p>

            <p>يحق للشركة تقديم إعلانات السياسة من وقت لآخر فيما يتعلق بمسائل مثل استحقاق الإجازة والأمومة</p>

            <p>الإجازة ، ومزايا الموظفين ، وساعات العمل ، وسياسات النقل ، وما إلى ذلك ، ويمكن تغييرها من وقت لآخر وفقًا لتقديرها الخاص.</p>

            <p>جميع قرارات سياسة الشركة هذه ملزمة لك ويجب أن تلغي هذه الاتفاقية إلى هذا الحد.</p>



            <p>14. القانون الحاكم / الاختصاص القضائي</p>

            <p>يخضع عملك في الشركة لقوانين الدولة. تخضع جميع النزاعات للاختصاص القضائي للمحكمة العليا</p>

            <p>غوجارات فقط.</p>



            <p>15. قبول عرضنا</p>

            <p>يرجى تأكيد قبولك لعقد العمل هذا من خلال التوقيع وإعادة النسخة المكررة.</p>



            <p>نرحب بكم ونتطلع إلى تلقي موافقتكم والعمل معكم.</p>



            <p>تفضلوا بقبول فائق الاحترام،</p>

            <p>{app_name}</p>

            <p>{date}</p>',
            'en' => '<h3 style="text-align: center;">Joining Letter</h3>
            <p>{date}</p>
            <p>{employee_name}</p>
            <p>{address}</p>
            <p>Subject: Appointment for the post of {designation}</p>
            <p>Dear {employee_name},</p>
            <p>We are pleased to offer you the position of {designation} with {app_name} theCompany on the following terms and</p>
            <p>conditions:</p>
            <p>1. Commencement of employment</p>
            <p>Your employment will be effective, as of {start_date}</p>
            <p>2. Job title</p>
            <p>Your job title will be{designation}.</p>
            <p>3. Salary</p>
            <p>Your salary and other benefits will be as set out in Schedule 1, hereto.</p>
            <p>4. Place of posting</p>
            <p>You will be posted at {branch}. You may however be required to work at any place of business which the Company has, or</p>
            <p>may later acquire.</p>
            <p>5. Hours of Work</p>
            <p>The normal working days are Monday through Friday. You will be required to work for such hours as necessary for the</p>
            <p>proper discharge of your duties to the Company. The normal working hours are from {start_time} to {end_time} and you are</p>
            <p>expected to work not less than {total_hours} hours each week, and if necessary for additional hours depending on your</p>
            <p>responsibilities.</p>
            <p>6. Leave/Holidays</p>
            <p>6.1 You are entitled to casual leave of 12 days.</p>
            <p>6.2 You are entitled to 12 working days of paid sick leave.</p>
            <p>6.3 The Company shall notify a list of declared holidays at the beginning of each year.</p>
            <p>7. Nature of duties</p>
            <p>You will perform to the best of your ability all the duties as are inherent in your post and such additional duties as the company</p>
            <p>may call upon you to perform, from time to time. Your specific duties are set out in Schedule II hereto.</p>
            <p>8. Company property</p>
            <p>You will always maintain in good condition Company property, which may be entrusted to you for official use during the course of</p>
            <p>your employment, and shall return all such property to the Company prior to relinquishment of your charge, failing which the cost</p>
            <p>of the same will be recovered from you by the Company.</p>
            <p>9. Borrowing/accepting gifts</p>
            <p>You will not borrow or accept any money, gift, reward, or compensation for your personal gains from or otherwise place yourself</p>
            <p>under pecuniary obligation to any person/client with whom you may be having official dealings.</p>
            <p>10. Termination</p>
            <p>10.1 Your appointment can be terminated by the Company, without any reason, by giving you not less than [Notice] months prior</p>
            <p>notice in writing or salary in lieu thereof. For the purpose of this clause, salary shall mean basic salary.</p>
            <p>10.2 You may terminate your employment with the Company, without any cause, by giving no less than [Employee Notice]</p>
            <p>months prior notice or salary for the unsaved period, left after adjustment of pending leaves, as on date.</p>
            <p>10.3 The Company reserves the right to terminate your employment summarily without any notice period or termination payment</p>
            <p>if it has reasonable ground to believe you are guilty of misconduct or negligence, or have committed any fundamental breach of</p>
            <p>contract, or caused any loss to the Company.</p>
            <p>10. 4 On the termination of your employment for whatever reason, you will return to the Company all property; documents, and</p>
            <p>paper, both original and copies thereof, including any samples, literature, contracts, records, lists, drawings, blueprints,</p>
            <p>letters, notes, data and the like; and Confidential Information, in your possession or under your control relating to your</p>
            <p>employment or to clients business affairs.</p>
            <p>11. Confidential Information</p>
            <p>11. 1 During your employment with the Company you will devote your whole time, attention, and skill to the best of your ability for</p>
            <p>its business. You shall not, directly or indirectly, engage or associate yourself with, be connected with, concerned, employed, or</p>
            <p>time or pursue any course of study whatsoever, without the prior permission of the Company.engaged in any other business or</p>
            <p>activities or any other post or work part-time or pursue any course of study whatsoever, without the prior permission of the</p>
            <p>Company.</p>
            <p>11.2 You must always maintain the highest degree of confidentiality and keep as confidential the records, documents, and other</p>
            <p>Confidential Information relating to the business of the Company which may be known to you or confided in you by any means</p>
            <p>and you will use such records, documents and information only in a duly authorized manner in the interest of the Company. For</p>
            <p>the purposes of this clauseConfidential Information means information about the Companys business and that of its customers</p>
            <p>which is not available to the general public and which may be learned by you in the course of your employment. This includes,</p>
            <p>but is not limited to, information relating to the organization, its customer lists, employment policies, personnel, and information</p>
            <p>about the Companys products, processes including ideas, concepts, projections, technology, manuals, drawing, designs,</p>
            <p>specifications, and all papers, resumes, records and other documents containing such Confidential Information.</p>
            <p>11.3 At no time, will you remove any Confidential Information from the office without permission.</p>
            <p>11.4 Your duty to safeguard and not disclos</p>
            <p>e Confidential Information will survive the expiration or termination of this Agreement and/or your employment with the Company.</p>
            <p>11.5 Breach of the conditions of this clause will render you liable to summary dismissal under the clause above in addition to any</p>
            <p>other remedy the Company may have against you in law.</p>
            <p>12. Notices</p>
            <p>Notices may be given by you to the Company at its registered office address. Notices may be given by the Company to you at</p>
            <p>the address intimated by you in the official records.</p>
            <p>13. Applicability of Company Policy</p>
            <p>The Company shall be entitled to make policy declarations from time to time pertaining to matters like leave entitlement,maternity</p>
            <p>leave, employees benefits, working hours, transfer policies, etc., and may alter the same from time to time at its sole discretion.</p>
            <p>All such policy decisions of the Company shall be binding on you and shall override this Agreement to that extent.</p>
            <p>14. Governing Law/Jurisdiction</p>
            <p>Your employment with the Company is subject to Country laws. All disputes shall be subject to the jurisdiction of High Court</p>
            <p>Gujarat only.</p>
            <p>15. Acceptance of our offer</p>
            <p>Please confirm your acceptance of this Contract of Employment by signing and returning the duplicate copy.</p>
            <p>We welcome you and look forward to receiving your acceptance and to working with you.</p>
            <p>Yours Sincerely,</p>
            <p>{app_name}</p>
            <p>{date}</p>',


            'fr' => '<h3 style="text-align: center;">Lettre dadh&eacute;sion</h3>

            <p>{date}</p>

            <p>{employee_name}</p>
            <p>{address}</p>


            <p>Objet : Nomination pour le poste de {designation}</p>



            <p>Cher {employee_name},</p>


            <p>Nous sommes heureux de vous proposer le poste de {designation} avec {app_name} la "Soci&eacute;t&eacute;" selon les conditions suivantes et</p>

            <p>les conditions:</p>

            <p>1. Entr&eacute;e en fonction</p>

            <p>Votre emploi sera effectif &agrave; partir du {start_date}</p>



            <p>2. Intitul&eacute; du poste</p>

            <p>Votre titre de poste sera {designation}.</p>



            <p>3. Salaire</p>

            <p>Votre salaire et vos autres avantages seront tels quindiqu&eacute;s &agrave; lannexe 1 ci-jointe.</p>


            <p>4. Lieu de d&eacute;tachement</p>
            <p>Vous serez affect&eacute; &agrave; {branch}. Vous pouvez cependant &ecirc;tre tenu de travailler dans nimporte quel lieu daffaires que la Soci&eacute;t&eacute; a, ou</p>

            <p>pourra acqu&eacute;rir plus tard.</p>



            <p>5. Heures de travail</p>

            <p>Les jours ouvrables normaux sont du lundi au vendredi. Vous devrez travailler les heures n&eacute;cessaires &agrave; la</p>

            <p>lexercice correct de vos fonctions envers la Soci&eacute;t&eacute;. Les heures normales de travail vont de {start_time} &agrave; {end_time} et vous &ecirc;tes</p>

            <p>devrait travailler au moins {total_hours} heures par semaine, et si n&eacute;cessaire des heures suppl&eacute;mentaires en fonction de votre</p>

            <p>responsabilit&eacute;s.</p>

            <p>6. Cong&eacute;s/Vacances</p>

            <p>6.1 Vous avez droit &agrave; un cong&eacute; occasionnel de 12 jours.</p>

            <p>6.2 Vous avez droit &agrave; 12 jours ouvrables de cong&eacute; de maladie pay&eacute;.</p>

            <p>6.3 La Soci&eacute;t&eacute; communiquera une liste des jours f&eacute;ri&eacute;s d&eacute;clar&eacute;s au d&eacute;but de chaque ann&eacute;e.</p>



            <p>7. Nature des fonctions</p>

            <p>Vous ex&eacute;cuterez au mieux de vos capacit&eacute;s toutes les t&acirc;ches inh&eacute;rentes &agrave; votre poste et les t&acirc;ches suppl&eacute;mentaires que lentreprise</p>

            <p>peut faire appel &agrave; vous pour effectuer, de temps &agrave; autre. Vos fonctions sp&eacute;cifiques sont &eacute;nonc&eacute;es &agrave; lannexe II ci-jointe.</p>



            <p>8. Biens sociaux</p>

            <p>Vous maintiendrez toujours en bon &eacute;tat les biens de la Soci&eacute;t&eacute;, qui peuvent vous &ecirc;tre confi&eacute;s pour un usage officiel au cours de votre</p>

            <p>votre emploi, et doit restituer tous ces biens &agrave; la Soci&eacute;t&eacute; avant labandon de votre charge, &agrave; d&eacute;faut de quoi le co&ucirc;t</p>

            <p>de m&ecirc;me seront r&eacute;cup&eacute;r&eacute;s aupr&egrave;s de vous par la Soci&eacute;t&eacute;.</p>



            <p>9. Emprunter/accepter des cadeaux</p>

            <p>Vous nemprunterez ni naccepterez dargent, de cadeau, de r&eacute;compense ou de compensation pour vos gains personnels ou vous placerez autrement</p>

            <p>sous obligation p&eacute;cuniaire envers toute personne/client avec qui vous pourriez avoir des relations officielles.</p>
            <p>10. R&eacute;siliation</p>

            <p>10.1 Votre nomination peut &ecirc;tre r&eacute;sili&eacute;e par la Soci&eacute;t&eacute;, sans aucune raison, en vous donnant au moins [Pr&eacute;avis] mois avant</p>

            <p>un pr&eacute;avis &eacute;crit ou un salaire en tenant lieu. Aux fins de la pr&eacute;sente clause, salaire sentend du salaire de base.</p>

            <p>10.2 Vous pouvez r&eacute;silier votre emploi au sein de la Soci&eacute;t&eacute;, sans motif, en donnant au moins [Avis &agrave; lemploy&eacute;]</p>

            <p>mois de pr&eacute;avis ou de salaire pour la p&eacute;riode non &eacute;pargn&eacute;e, restant apr&egrave;s r&eacute;gularisation des cong&eacute;s en attente, &agrave; la date.</p>

            <p>10.3 La Soci&eacute;t&eacute; se r&eacute;serve le droit de r&eacute;silier votre emploi sans pr&eacute;avis ni indemnit&eacute; de licenciement.</p>

            <p>sil a des motifs raisonnables de croire que vous &ecirc;tes coupable dinconduite ou de n&eacute;gligence, ou que vous avez commis une violation fondamentale de</p>

            <p>contrat, ou caus&eacute; une perte &agrave; la Soci&eacute;t&eacute;.</p>

            <p>10. 4 &Agrave; la fin de votre emploi pour quelque raison que ce soit, vous restituerez &agrave; la Soci&eacute;t&eacute; tous les biens ; document, et</p>

            <p>papier, &agrave; la fois loriginal et les copies de celui-ci, y compris les &eacute;chantillons, la litt&eacute;rature, les contrats, les dossiers, les listes, les dessins, les plans,</p>

            <p>lettres, notes, donn&eacute;es et similaires; et Informations confidentielles, en votre possession ou sous votre contr&ocirc;le relatives &agrave; votre</p>

            <p>lemploi ou aux affaires commerciales des clients.</p>
            <p>11. Informations confidentielles</p>

            <p>11. 1 Au cours de votre emploi au sein de la Soci&eacute;t&eacute;, vous consacrerez tout votre temps, votre attention et vos comp&eacute;tences au mieux de vos capacit&eacute;s pour</p>

            <p>son affaire. Vous ne devez pas, directement ou indirectement, vous engager ou vous associer &agrave;, &ecirc;tre li&eacute; &agrave;, concern&eacute;, employ&eacute; ou</p>

            <p>temps ou poursuivre quelque programme d&eacute;tudes que ce soit, sans lautorisation pr&eacute;alable de la Soci&eacute;t&eacute;. engag&eacute; dans toute autre entreprise ou</p>

            <p>activit&eacute;s ou tout autre poste ou travail &agrave; temps partiel ou poursuivre des &eacute;tudes quelconques, sans lautorisation pr&eacute;alable du</p>

            <p>Compagnie.</p>

            <p>11.2 Vous devez toujours maintenir le plus haut degr&eacute; de confidentialit&eacute; et garder confidentiels les dossiers, documents et autres</p>

            <p>Informations confidentielles relatives &agrave; lactivit&eacute; de la Soci&eacute;t&eacute; dont vous pourriez avoir connaissance ou qui vous seraient confi&eacute;es par tout moyen</p>

            <p>et vous nutiliserez ces registres, documents et informations que dune mani&egrave;re d&ucirc;ment autoris&eacute;e dans lint&eacute;r&ecirc;t de la Soci&eacute;t&eacute;. Pour</p>

            <p>aux fins de la pr&eacute;sente clause &laquo; Informations confidentielles &raquo; d&eacute;signe les informations sur les activit&eacute;s de la Soci&eacute;t&eacute; et celles de ses clients</p>

            <p>qui nest pas accessible au grand public et dont vous pourriez avoir connaissance dans le cadre de votre emploi. Ceci comprend,</p>

            <p>mais sans sy limiter, les informations relatives &agrave; lorganisation, ses listes de clients, ses politiques demploi, son personnel et les informations</p>

            <p>sur les produits, les processus de la Soci&eacute;t&eacute;, y compris les id&eacute;es, les concepts, les projections, la technologie, les manuels, les dessins, les conceptions,</p>

            <p>sp&eacute;cifications, et tous les papiers, curriculum vitae, dossiers et autres documents contenant de telles informations confidentielles.</p>

            <p>11.3 &Agrave; aucun moment, vous ne retirerez des informations confidentielles du bureau sans autorisation.</p>

            <p>11.4 Votre devoir de prot&eacute;ger et de ne pas divulguer</p>

            <p>Les Informations confidentielles survivront &agrave; lexpiration ou &agrave; la r&eacute;siliation du pr&eacute;sent Contrat et/ou &agrave; votre emploi au sein de la Soci&eacute;t&eacute;.</p>

            <p>11.5 La violation des conditions de cette clause vous rendra passible dun renvoi sans pr&eacute;avis en vertu de la clause ci-dessus en plus de tout</p>

            <p>autre recours que la Soci&eacute;t&eacute; peut avoir contre vous en droit.</p>
            <p>12. Avis</p>

            <p>Des avis peuvent &ecirc;tre donn&eacute;s par vous &agrave; la Soci&eacute;t&eacute; &agrave; ladresse de son si&egrave;ge social. Des avis peuvent vous &ecirc;tre donn&eacute;s par la Soci&eacute;t&eacute; &agrave;</p>

            <p>ladresse que vous avez indiqu&eacute;e dans les registres officiels.</p>



            <p>13. Applicabilit&eacute; de la politique de lentreprise</p>

            <p>La Soci&eacute;t&eacute; est autoris&eacute;e &agrave; faire des d&eacute;clarations de politique de temps &agrave; autre concernant des questions telles que le droit aux cong&eacute;s, la maternit&eacute;</p>

            <p>les cong&eacute;s, les avantages sociaux des employ&eacute;s, les heures de travail, les politiques de transfert, etc., et peut les modifier de temps &agrave; autre &agrave; sa seule discr&eacute;tion.</p>

            <p>Toutes ces d&eacute;cisions politiques de la Soci&eacute;t&eacute; vous lieront et pr&eacute;vaudront sur le pr&eacute;sent Contrat dans cette mesure.</p>



            <p>14. Droit applicable/juridiction</p>

            <p>Votre emploi au sein de la Soci&eacute;t&eacute; est soumis aux lois du pays. Tous les litiges seront soumis &agrave; la comp&eacute;tence du tribunal de grande instance</p>

            <p>Gujarat uniquement.</p>



            <p>15. Acceptation de notre offre</p>

            <p>Veuillez confirmer votre acceptation de ce contrat de travail en signant et en renvoyant le duplicata.</p>



            <p>Nous vous souhaitons la bienvenue et nous nous r&eacute;jouissons de recevoir votre acceptation et de travailler avec vous.</p>



            <p>Cordialement,</p>

            <p>{app_name}</p>

            <p>{date}</p>',

     ];

        foreach($defaultTemplate as $lang => $content)
        {
            JoiningLetter::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => 2,

                ]
            );
        }

    }
    public static function defaultJoiningLetterRegister($user_id)
    {

        $defaultTemplate = [

            'ar' => '<h2 style="text-align: center;"><strong>خطاب الانضمام</strong></h2>
            <p>{date}</p>
            <p>{employee_name}</p>
            <p>{address}</p>
            <p>الموضوع: موعد لوظيفة {designation}</p>
            <p>عزيزي {employee_name} ،</p>
            <p>يسعدنا أن نقدم لك منصب {designation} مع {app_name} "الشركة" وفقًا للشروط التالية و</p>
            <p>الظروف:</p>
            <p>1. بدء العمل</p>
            <p>سيصبح عملك ساريًا اعتبارًا من {start_date}</p>
            <p>2. المسمى الوظيفي</p>
            <p>سيكون المسمى الوظيفي الخاص بك هو {designation}.</p>
            <p>3. الراتب</p>
            <p>سيكون راتبك والمزايا الأخرى على النحو المبين في الجدول 1 ، طيه.</p>
            <p>4. مكان الإرسال</p>
            <p>سيتم إرسالك إلى {branch}. ومع ذلك ، قد يُطلب منك العمل في أي مكان عمل تمتلكه الشركة ، أو</p>
            <p>قد تحصل لاحقًا.</p>
            <p>5. ساعات العمل</p>
            <p>أيام العمل العادية هي من الاثنين إلى الجمعة. سيُطلب منك العمل لساعات حسب الضرورة لـ</p>
            <p>أداء واجباتك على النحو الصحيح تجاه الشركة. ساعات العمل العادية من {start_time} إلى {end_time} وأنت</p>
            <p>من المتوقع أن يعمل ما لا يقل عن {total_hours} ساعة كل أسبوع ، وإذا لزم الأمر لساعات إضافية اعتمادًا على</p>
            <p>المسؤوليات.</p>
            <p>6. الإجازة / العطل</p>
            <p>6.1 يحق لك الحصول على إجازة غير رسمية مدتها 12 يومًا.</p>
            <p>6.2 يحق لك الحصول على إجازة مرضية مدفوعة الأجر لمدة 12 يوم عمل.</p>
            <p>6.3 تخطر الشركة بقائمة الإجازات المعلنة في بداية كل عام.</p>
            <p>7. طبيعة الواجبات</p>
            <p>ستقوم بأداء أفضل ما لديك من واجبات متأصلة في منصبك ومهام إضافية مثل الشركة</p>
            <p>قد يدعوك لأداء ، من وقت لآخر. واجباتك المحددة منصوص عليها في الجدول الثاني بهذه الرسالة.</p>
            <p>8. ممتلكات الشركة</p>
            <p>ستحافظ دائمًا على ممتلكات الشركة في حالة جيدة ، والتي قد يتم تكليفك بها للاستخدام الرسمي خلال فترة عملها</p>
            <p>عملك ، ويجب أن تعيد جميع هذه الممتلكات إلى الشركة قبل التخلي عن الرسوم الخاصة بك ، وإلا فإن التكلفة</p>
            <p>نفس الشيء سوف تسترده منك الشركة.</p>
            <p>9. الاقتراض / قبول الهدايا</p>
            <p>لن تقترض أو تقبل أي أموال أو هدية أو مكافأة أو تعويض مقابل مكاسبك الشخصية من أو تضع نفسك بأي طريقة أخرى</p>
            <p>بموجب التزام مالي تجاه أي شخص / عميل قد تكون لديك تعاملات رسمية معه.</p>
            <p>10. الإنهاء</p>
            <p>10.1 يمكن للشركة إنهاء موعدك ، دون أي سبب ، من خلال إعطائك ما لا يقل عن [إشعار] قبل أشهر</p>
            <p>إشعار خطي أو راتب بدلاً منه. لغرض هذا البند ، يقصد بالراتب المرتب الأساسي.</p>
            <p>10.2 إنهاء عملك مع الشركة ، دون أي سبب ، من خلال تقديم ما لا يقل عن إشعار الموظف</p>
            <p>أشهر الإخطار أو الراتب عن الفترة غير المحفوظة ، المتبقية بعد تعديل الإجازات المعلقة ، كما في التاريخ.</p>
            <p>10.3 تحتفظ الشركة بالحق في إنهاء عملك بإيجاز دون أي فترة إشعار أو مدفوعات إنهاء</p>
            <p>إذا كان لديه سبب معقول للاعتقاد بأنك مذنب بسوء السلوك أو الإهمال ، أو ارتكبت أي خرق جوهري لـ</p>
            <p>العقد ، أو تسبب في أي خسارة للشركة.</p>
            <p>10. 4 عند إنهاء عملك لأي سبب من الأسباب ، ستعيد إلى الشركة جميع ممتلكاتك ؛ المستندات و</p>
            <p>الأوراق الأصلية ونسخها ، بما في ذلك أي عينات ، وأدبيات ، وعقود ، وسجلات ، وقوائم ، ورسومات ، ومخططات ،</p>
            <p>الرسائل والملاحظات والبيانات وما شابه ذلك ؛ والمعلومات السرية التي بحوزتك أو تحت سيطرتك والمتعلقة بك</p>
            <p>التوظيف أو الشؤون التجارية للعملاء.</p>
            <p>11. المعلومات السرية</p>
            <p>11. 1 أثناء عملك في الشركة ، سوف تكرس وقتك واهتمامك ومهارتك كلها بأفضل ما لديك من قدرات</p>
            <p>عملها. لا يجوز لك ، بشكل مباشر أو غير مباشر ، الانخراط أو الارتباط بنفسك ، أو الارتباط به ، أو القلق ، أو التوظيف ، أو</p>
            <p>الوقت أو متابعة أي دورة دراسية على الإطلاق ، دون الحصول على إذن مسبق من الشركة أو الانخراط في أي عمل آخر أو</p>
            <p>الأنشطة أو أي وظيفة أخرى أو العمل بدوام جزئي أو متابعة أي دورة دراسية على الإطلاق ، دون إذن مسبق من</p>
            <p>شركة.</p>
            <p>11. المعلومات السرية</p>
            <p>11. 1 أثناء عملك في الشركة ، سوف تكرس وقتك واهتمامك ومهارتك كلها بأفضل ما لديك من قدرات</p>
            <p>عملها. لا يجوز لك ، بشكل مباشر أو غير مباشر ، الانخراط أو الارتباط بنفسك ، أو الارتباط به ، أو القلق ، أو التوظيف ، أو</p>
            <p>الوقت أو متابعة أي دورة دراسية على الإطلاق ، دون الحصول على إذن مسبق من الشركة أو الانخراط في أي عمل آخر أو</p>
            <p>الأنشطة أو أي وظيفة أخرى أو العمل بدوام جزئي أو متابعة أي دورة دراسية على الإطلاق ، دون إذن مسبق من</p>
            <p>شركة.</p>
            <p>11.2 يجب عليك دائمًا الحفاظ على أعلى درجة من السرية والحفاظ على سرية السجلات والوثائق وغيرها</p>
            <p>المعلومات السرية المتعلقة بأعمال الشركة والتي قد تكون معروفة لك أو مخولة لك بأي وسيلة</p>
            <p>ولن تستخدم هذه السجلات والمستندات والمعلومات إلا بالطريقة المصرح بها حسب الأصول لصالح الشركة. إلى عن على</p>
            <p>أغراض هذا البند "المعلومات السرية" تعني المعلومات المتعلقة بأعمال الشركة وعملائها</p>
            <p>التي لا تتوفر لعامة الناس والتي قد تتعلمها أثناء عملك. هذا يشمل،</p>
            <p>على سبيل المثال لا الحصر ، المعلومات المتعلقة بالمنظمة وقوائم العملاء وسياسات التوظيف والموظفين والمعلومات</p>
            <p>حول منتجات الشركة وعملياتها بما في ذلك الأفكار والمفاهيم والإسقاطات والتكنولوجيا والكتيبات والرسم والتصاميم ،</p>
            <p>المواصفات وجميع الأوراق والسير الذاتية والسجلات والمستندات الأخرى التي تحتوي على هذه المعلومات السرية.</p>
            <p>11.3 لن تقوم في أي وقت بإزالة أي معلومات سرية من المكتب دون إذن.</p>
            <p>11.4 واجبك في الحماية وعدم الإفشاء</p>
            <p>تظل المعلومات السرية سارية بعد انتهاء أو إنهاء هذه الاتفاقية و / أو عملك مع الشركة.</p>
            <p>11.5 سوف يجعلك خرق شروط هذا البند عرضة للفصل بإجراءات موجزة بموجب الفقرة أعلاه بالإضافة إلى أي</p>
            <p>أي تعويض آخر قد يكون للشركة ضدك في القانون.</p>
            <p>12. الإخطارات</p>
            <p>يجوز لك إرسال إخطارات إلى الشركة على عنوان مكتبها المسجل. يمكن أن ترسل لك الشركة إشعارات على</p>
            <p>العنوان الذي أشرت إليه في السجلات الرسمية.</p>
            <p>13. تطبيق سياسة الشركة</p>
            <p>يحق للشركة تقديم إعلانات السياسة من وقت لآخر فيما يتعلق بمسائل مثل استحقاق الإجازة والأمومة</p>
            <p>الإجازة ، ومزايا الموظفين ، وساعات العمل ، وسياسات النقل ، وما إلى ذلك ، ويمكن تغييرها من وقت لآخر وفقًا لتقديرها الخاص.</p>
            <p>جميع قرارات سياسة الشركة هذه ملزمة لك ويجب أن تلغي هذه الاتفاقية إلى هذا الحد.</p>
            <p>14. القانون الحاكم / الاختصاص القضائي</p>
            <p>يخضع عملك في الشركة لقوانين الدولة. تخضع جميع النزاعات للاختصاص القضائي للمحكمة العليا</p>
            <p>غوجارات فقط.</p>
            <p>15. قبول عرضنا</p>
            <p>يرجى تأكيد قبولك لعقد العمل هذا من خلال التوقيع وإعادة النسخة المكررة.</p>
            <p>نرحب بكم ونتطلع إلى تلقي موافقتكم والعمل معكم.</p>
            <p>تفضلوا بقبول فائق الاحترام،</p>
            <p>{app_name}</p>
            <p>{date}</p>',

            'en' => '<h3 style="text-align: center;">Joining Letter</h3>
            <p>{date}</p>
            <p>{employee_name}</p>
            <p>{address}</p>
            <p>Subject: Appointment for the post of {designation}</p>
            <p>Dear {employee_name},</p>
            <p>We are pleased to offer you the position of {designation} with {app_name} theCompany on the following terms and</p>
            <p>conditions:</p>
            <p>1. Commencement of employment</p>
            <p>Your employment will be effective, as of {start_date}</p>
            <p>2. Job title</p>
            <p>Your job title will be{designation}.</p>
            <p>3. Salary</p>
            <p>Your salary and other benefits will be as set out in Schedule 1, hereto.</p>
            <p>4. Place of posting</p>
            <p>You will be posted at {branch}. You may however be required to work at any place of business which the Company has, or</p>
            <p>may later acquire.</p>
            <p>5. Hours of Work</p>
            <p>The normal working days are Monday through Friday. You will be required to work for such hours as necessary for the</p>
            <p>proper discharge of your duties to the Company. The normal working hours are from {start_time} to {end_time} and you are</p>
            <p>expected to work not less than {total_hours} hours each week, and if necessary for additional hours depending on your</p>
            <p>responsibilities.</p>
            <p>6. Leave/Holidays</p>
            <p>6.1 You are entitled to casual leave of 12 days.</p>
            <p>6.2 You are entitled to 12 working days of paid sick leave.</p>
            <p>6.3 The Company shall notify a list of declared holidays at the beginning of each year.</p>
            <p>7. Nature of duties</p>
            <p>You will perform to the best of your ability all the duties as are inherent in your post and such additional duties as the company</p>
            <p>may call upon you to perform, from time to time. Your specific duties are set out in Schedule II hereto.</p>
            <p>8. Company property</p>
            <p>You will always maintain in good condition Company property, which may be entrusted to you for official use during the course of</p>
            <p>your employment, and shall return all such property to the Company prior to relinquishment of your charge, failing which the cost</p>
            <p>of the same will be recovered from you by the Company.</p>
            <p>9. Borrowing/accepting gifts</p>
            <p>You will not borrow or accept any money, gift, reward, or compensation for your personal gains from or otherwise place yourself</p>
            <p>under pecuniary obligation to any person/client with whom you may be having official dealings.</p>
            <p>10. Termination</p>
            <p>10.1 Your appointment can be terminated by the Company, without any reason, by giving you not less than [Notice] months prior</p>
            <p>notice in writing or salary in lieu thereof. For the purpose of this clause, salary shall mean basic salary.</p>
            <p>10.2 You may terminate your employment with the Company, without any cause, by giving no less than [Employee Notice]</p>
            <p>months prior notice or salary for the unsaved period, left after adjustment of pending leaves, as on date.</p>
            <p>10.3 The Company reserves the right to terminate your employment summarily without any notice period or termination payment</p>
            <p>if it has reasonable ground to believe you are guilty of misconduct or negligence, or have committed any fundamental breach of</p>
            <p>contract, or caused any loss to the Company.</p>
            <p>10. 4 On the termination of your employment for whatever reason, you will return to the Company all property; documents, and</p>
            <p>paper, both original and copies thereof, including any samples, literature, contracts, records, lists, drawings, blueprints,</p>
            <p>letters, notes, data and the like; and Confidential Information, in your possession or under your control relating to your</p>
            <p>employment or to clients business affairs.</p>
            <p>11. Confidential Information</p>
            <p>11. 1 During your employment with the Company you will devote your whole time, attention, and skill to the best of your ability for</p>
            <p>its business. You shall not, directly or indirectly, engage or associate yourself with, be connected with, concerned, employed, or</p>
            <p>time or pursue any course of study whatsoever, without the prior permission of the Company.engaged in any other business or</p>
            <p>activities or any other post or work part-time or pursue any course of study whatsoever, without the prior permission of the</p>
            <p>Company.</p>
            <p>11.2 You must always maintain the highest degree of confidentiality and keep as confidential the records, documents, and other</p>
            <p>Confidential Information relating to the business of the Company which may be known to you or confided in you by any means</p>
            <p>and you will use such records, documents and information only in a duly authorized manner in the interest of the Company. For</p>
            <p>the purposes of this clauseConfidential Information means information about the Companys business and that of its customers</p>
            <p>which is not available to the general public and which may be learned by you in the course of your employment. This includes,</p>
            <p>but is not limited to, information relating to the organization, its customer lists, employment policies, personnel, and information</p>
            <p>about the Companys products, processes including ideas, concepts, projections, technology, manuals, drawing, designs,</p>
            <p>specifications, and all papers, resumes, records and other documents containing such Confidential Information.</p>
            <p>11.3 At no time, will you remove any Confidential Information from the office without permission.</p>
            <p>11.4 Your duty to safeguard and not disclos</p>
            <p>e Confidential Information will survive the expiration or termination of this Agreement and/or your employment with the Company.</p>
            <p>11.5 Breach of the conditions of this clause will render you liable to summary dismissal under the clause above in addition to any</p>
            <p>other remedy the Company may have against you in law.</p>
            <p>12. Notices</p>
            <p>Notices may be given by you to the Company at its registered office address. Notices may be given by the Company to you at</p>
            <p>the address intimated by you in the official records.</p>
            <p>13. Applicability of Company Policy</p>
            <p>The Company shall be entitled to make policy declarations from time to time pertaining to matters like leave entitlement,maternity</p>
            <p>leave, employees benefits, working hours, transfer policies, etc., and may alter the same from time to time at its sole discretion.</p>
            <p>All such policy decisions of the Company shall be binding on you and shall override this Agreement to that extent.</p>
            <p>14. Governing Law/Jurisdiction</p>
            <p>Your employment with the Company is subject to Country laws. All disputes shall be subject to the jurisdiction of High Court</p>
            <p>Gujarat only.</p>
            <p>15. Acceptance of our offer</p>
            <p>Please confirm your acceptance of this Contract of Employment by signing and returning the duplicate copy.</p>
            <p>We welcome you and look forward to receiving your acceptance and to working with you.</p>
            <p>Yours Sincerely,</p>
            <p>{app_name}</p>
            <p>{date}</p>',

            'fr' => '<h3 style="text-align: center;">Lettre dadh&eacute;sion</h3>


            <p>{date}</p>

            <p>{employee_name}</p>
            <p>{address}</p>


            <p>Objet : Nomination pour le poste de {designation}</p>



            <p>Cher {employee_name},</p>


            <p>Nous sommes heureux de vous proposer le poste de {designation} avec {app_name} la "Soci&eacute;t&eacute;" selon les conditions suivantes et</p>

            <p>les conditions:</p>

            <p>1. Entr&eacute;e en fonction</p>

            <p>Votre emploi sera effectif &agrave; partir du {start_date}</p>



            <p>2. Intitul&eacute; du poste</p>

            <p>Votre titre de poste sera {designation}.</p>



            <p>3. Salaire</p>

            <p>Votre salaire et vos autres avantages seront tels quindiqu&eacute;s &agrave; lannexe 1 ci-jointe.</p>


            <p>4. Lieu de d&eacute;tachement</p>
            <p>Vous serez affect&eacute; &agrave; {branch}. Vous pouvez cependant &ecirc;tre tenu de travailler dans nimporte quel lieu daffaires que la Soci&eacute;t&eacute; a, ou</p>

            <p>pourra acqu&eacute;rir plus tard.</p>



            <p>5. Heures de travail</p>

            <p>Les jours ouvrables normaux sont du lundi au vendredi. Vous devrez travailler les heures n&eacute;cessaires &agrave; la</p>

            <p>lexercice correct de vos fonctions envers la Soci&eacute;t&eacute;. Les heures normales de travail vont de {start_time} &agrave; {end_time} et vous &ecirc;tes</p>

            <p>devrait travailler au moins {total_hours} heures par semaine, et si n&eacute;cessaire des heures suppl&eacute;mentaires en fonction de votre</p>

            <p>responsabilit&eacute;s.</p>

            <p>6. Cong&eacute;s/Vacances</p>

            <p>6.1 Vous avez droit &agrave; un cong&eacute; occasionnel de 12 jours.</p>

            <p>6.2 Vous avez droit &agrave; 12 jours ouvrables de cong&eacute; de maladie pay&eacute;.</p>

            <p>6.3 La Soci&eacute;t&eacute; communiquera une liste des jours f&eacute;ri&eacute;s d&eacute;clar&eacute;s au d&eacute;but de chaque ann&eacute;e.</p>



            <p>7. Nature des fonctions</p>

            <p>Vous ex&eacute;cuterez au mieux de vos capacit&eacute;s toutes les t&acirc;ches inh&eacute;rentes &agrave; votre poste et les t&acirc;ches suppl&eacute;mentaires que lentreprise</p>

            <p>peut faire appel &agrave; vous pour effectuer, de temps &agrave; autre. Vos fonctions sp&eacute;cifiques sont &eacute;nonc&eacute;es &agrave; lannexe II ci-jointe.</p>



            <p>8. Biens sociaux</p>

            <p>Vous maintiendrez toujours en bon &eacute;tat les biens de la Soci&eacute;t&eacute;, qui peuvent vous &ecirc;tre confi&eacute;s pour un usage officiel au cours de votre</p>

            <p>votre emploi, et doit restituer tous ces biens &agrave; la Soci&eacute;t&eacute; avant labandon de votre charge, &agrave; d&eacute;faut de quoi le co&ucirc;t</p>

            <p>de m&ecirc;me seront r&eacute;cup&eacute;r&eacute;s aupr&egrave;s de vous par la Soci&eacute;t&eacute;.</p>



            <p>9. Emprunter/accepter des cadeaux</p>

            <p>Vous nemprunterez ni naccepterez dargent, de cadeau, de r&eacute;compense ou de compensation pour vos gains personnels ou vous placerez autrement</p>

            <p>sous obligation p&eacute;cuniaire envers toute personne/client avec qui vous pourriez avoir des relations officielles.</p>
            <p>10. R&eacute;siliation</p>

            <p>10.1 Votre nomination peut &ecirc;tre r&eacute;sili&eacute;e par la Soci&eacute;t&eacute;, sans aucune raison, en vous donnant au moins [Pr&eacute;avis] mois avant</p>

            <p>un pr&eacute;avis &eacute;crit ou un salaire en tenant lieu. Aux fins de la pr&eacute;sente clause, salaire sentend du salaire de base.</p>

            <p>10.2 Vous pouvez r&eacute;silier votre emploi au sein de la Soci&eacute;t&eacute;, sans motif, en donnant au moins [Avis &agrave; lemploy&eacute;]</p>

            <p>mois de pr&eacute;avis ou de salaire pour la p&eacute;riode non &eacute;pargn&eacute;e, restant apr&egrave;s r&eacute;gularisation des cong&eacute;s en attente, &agrave; la date.</p>

            <p>10.3 La Soci&eacute;t&eacute; se r&eacute;serve le droit de r&eacute;silier votre emploi sans pr&eacute;avis ni indemnit&eacute; de licenciement.</p>

            <p>sil a des motifs raisonnables de croire que vous &ecirc;tes coupable dinconduite ou de n&eacute;gligence, ou que vous avez commis une violation fondamentale de</p>

            <p>contrat, ou caus&eacute; une perte &agrave; la Soci&eacute;t&eacute;.</p>

            <p>10. 4 &Agrave; la fin de votre emploi pour quelque raison que ce soit, vous restituerez &agrave; la Soci&eacute;t&eacute; tous les biens ; document, et</p>

            <p>papier, &agrave; la fois loriginal et les copies de celui-ci, y compris les &eacute;chantillons, la litt&eacute;rature, les contrats, les dossiers, les listes, les dessins, les plans,</p>

            <p>lettres, notes, donn&eacute;es et similaires; et Informations confidentielles, en votre possession ou sous votre contr&ocirc;le relatives &agrave; votre</p>

            <p>lemploi ou aux affaires commerciales des clients.</p>
            <p>11. Informations confidentielles</p>

            <p>11. 1 Au cours de votre emploi au sein de la Soci&eacute;t&eacute;, vous consacrerez tout votre temps, votre attention et vos comp&eacute;tences au mieux de vos capacit&eacute;s pour</p>

            <p>son affaire. Vous ne devez pas, directement ou indirectement, vous engager ou vous associer &agrave;, &ecirc;tre li&eacute; &agrave;, concern&eacute;, employ&eacute; ou</p>

            <p>temps ou poursuivre quelque programme d&eacute;tudes que ce soit, sans lautorisation pr&eacute;alable de la Soci&eacute;t&eacute;. engag&eacute; dans toute autre entreprise ou</p>

            <p>activit&eacute;s ou tout autre poste ou travail &agrave; temps partiel ou poursuivre des &eacute;tudes quelconques, sans lautorisation pr&eacute;alable du</p>

            <p>Compagnie.</p>

            <p>11.2 Vous devez toujours maintenir le plus haut degr&eacute; de confidentialit&eacute; et garder confidentiels les dossiers, documents et autres</p>

            <p>Informations confidentielles relatives &agrave; lactivit&eacute; de la Soci&eacute;t&eacute; dont vous pourriez avoir connaissance ou qui vous seraient confi&eacute;es par tout moyen</p>

            <p>et vous nutiliserez ces registres, documents et informations que dune mani&egrave;re d&ucirc;ment autoris&eacute;e dans lint&eacute;r&ecirc;t de la Soci&eacute;t&eacute;. Pour</p>

            <p>aux fins de la pr&eacute;sente clause &laquo; Informations confidentielles &raquo; d&eacute;signe les informations sur les activit&eacute;s de la Soci&eacute;t&eacute; et celles de ses clients</p>

            <p>qui nest pas accessible au grand public et dont vous pourriez avoir connaissance dans le cadre de votre emploi. Ceci comprend,</p>

            <p>mais sans sy limiter, les informations relatives &agrave; lorganisation, ses listes de clients, ses politiques demploi, son personnel et les informations</p>

            <p>sur les produits, les processus de la Soci&eacute;t&eacute;, y compris les id&eacute;es, les concepts, les projections, la technologie, les manuels, les dessins, les conceptions,</p>

            <p>sp&eacute;cifications, et tous les papiers, curriculum vitae, dossiers et autres documents contenant de telles informations confidentielles.</p>

            <p>11.3 &Agrave; aucun moment, vous ne retirerez des informations confidentielles du bureau sans autorisation.</p>

            <p>11.4 Votre devoir de prot&eacute;ger et de ne pas divulguer</p>

            <p>Les Informations confidentielles survivront &agrave; lexpiration ou &agrave; la r&eacute;siliation du pr&eacute;sent Contrat et/ou &agrave; votre emploi au sein de la Soci&eacute;t&eacute;.</p>

            <p>11.5 La violation des conditions de cette clause vous rendra passible dun renvoi sans pr&eacute;avis en vertu de la clause ci-dessus en plus de tout</p>

            <p>autre recours que la Soci&eacute;t&eacute; peut avoir contre vous en droit.</p>
            <p>12. Avis</p>

            <p>Des avis peuvent &ecirc;tre donn&eacute;s par vous &agrave; la Soci&eacute;t&eacute; &agrave; ladresse de son si&egrave;ge social. Des avis peuvent vous &ecirc;tre donn&eacute;s par la Soci&eacute;t&eacute; &agrave;</p>

            <p>ladresse que vous avez indiqu&eacute;e dans les registres officiels.</p>



            <p>13. Applicabilit&eacute; de la politique de lentreprise</p>

            <p>La Soci&eacute;t&eacute; est autoris&eacute;e &agrave; faire des d&eacute;clarations de politique de temps &agrave; autre concernant des questions telles que le droit aux cong&eacute;s, la maternit&eacute;</p>

            <p>les cong&eacute;s, les avantages sociaux des employ&eacute;s, les heures de travail, les politiques de transfert, etc., et peut les modifier de temps &agrave; autre &agrave; sa seule discr&eacute;tion.</p>

            <p>Toutes ces d&eacute;cisions politiques de la Soci&eacute;t&eacute; vous lieront et pr&eacute;vaudront sur le pr&eacute;sent Contrat dans cette mesure.</p>



            <p>14. Droit applicable/juridiction</p>

            <p>Votre emploi au sein de la Soci&eacute;t&eacute; est soumis aux lois du pays. Tous les litiges seront soumis &agrave; la comp&eacute;tence du tribunal de grande instance</p>

            <p>Gujarat uniquement.</p>



            <p>15. Acceptation de notre offre</p>

            <p>Veuillez confirmer votre acceptation de ce contrat de travail en signant et en renvoyant le duplicata.</p>



            <p>Nous vous souhaitons la bienvenue et nous nous r&eacute;jouissons de recevoir votre acceptation et de travailler avec vous.</p>



            <p>Cordialement,</p>

            <p>{app_name}</p>

            <p>{date}</p>',

     ];

        foreach($defaultTemplate as $lang => $content)
        {
            JoiningLetter::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => $user_id,

                ]
            );
        }

    }


}
