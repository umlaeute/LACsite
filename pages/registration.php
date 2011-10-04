<h1>Registration policy</h1>
<p>
Registration and admittance to LAC 2012 is free but if you want to attend 
 <span class="standout">you need to register</span> (scroll down).
</p>

This is for a number of reasons:
<ul>
<li> Estimate - We need an estimate of the number of attendees </li>
<li> ID - We'd like a name to print on your badge </li>
<li> We want to be able to contact you (see under Privacy) </li>
</ul>

<p>
If you have registered and for whatever reason will not come to the
conference, please let us know as soon as you can by sending an email to
lac -AT- linuxaudio -DOT- org.
</p>

<h1>Privacy</h1>
<p>
You are asked for your e-mail address so we can follow up on your
registration on a later date. We will not use your email address for any
other purpose than to inform you of conference details,
last minute changes or to confirm unsubscription requests and
will keep mail traffic to an absolute minimum.
Personal data provided by you in the registration form will be kept confidential and is not shared with any 3rd party.
</p>

<h1>Registration</h1>

<div id="registration">
<p>Please enter your registration information; fields marked with a
 <span class="error">*</span> are mandatory.</p>

<?php

  echo $errmsg;

?>

<?php 
  function _ck($k, $c) {
    if (isset($_POST[$k]) && $_POST[$k] == $c) echo ' checked="checked"';
  }

  function _cl($k, $c) {
    if (isset($_POST[$k]) && $_POST[$k] == $c) return ' checked="checked"';
    return '';
  }

  function _sl($k, $c) {
    if (isset($_POST[$k]) && $_POST[$k] == $c) return ' selected="selected"';
    return '';
  }

  function gen_options ($d,$k) {
    foreach ($d as $v => $t) {
      echo '    <option value="'.$v.'"'._sl($k,$v).'>'.$t.'</option>'."\n";
    }
  }

  function gen_checktd ($d,$r=3) {
    $cnt=0;
    foreach ($d as $v => $t) {
      echo '    <td><label><input type="checkbox" name="'.$v.'" value="1"'._cl($v,1).'/>'.$t.'</label></td>'."\n";
      if (++$cnt%$r == 0) echo "    </tr><tr>\n";
    }
  }

  $ages=array (
   '' => 'Please select your age group..',
   'A1520' => '15-20 years',
   'A2125' => '21-25 years',
   'A2530' => '25-30 years',
   'A3135' => '31-35 years',
   'A3540' => '35-40 years',
   'A4145' => '41-45 years',
   'A4550' => '45-50 years',
   'A5155' => '51-55 years',
   'A5560' => '55-60 years',
   'A6165' => '61-65 years',
   'A6570' => '65-70 years',
   'A71XX' => '71 and older'
  );

  $ctry=array (
      '' => 'Please select your country..',
    'AF' => 'AF (AFGHANISTAN)',
    'AX' => 'AX (ALAND ISLANDS)',
    'AL' => 'AL (ALBANIA)',
    'DZ' => 'DZ (ALGERIA)',
    'AS' => 'AS (AMERICAN SAMOA)',
    'AD' => 'AD (ANDORRA)',
    'AO' => 'AO (ANGOLA)',
    'AI' => 'AI (ANGUILLA)',
    'AQ' => 'AQ (ANTARCTICA)',
    'AG' => 'AG (ANTIGUA AND BARBUDA)',
    'AR' => 'AR (ARGENTINA)',
    'AM' => 'AM (ARMENIA)',
    'AW' => 'AW (ARUBA)',
    'AU' => 'AU (AUSTRALIA)',
    'AT' => 'AT (AUSTRIA)',
    'AZ' => 'AZ (AZERBAIJAN)',
    'BS' => 'BS (BAHAMAS)',
    'BH' => 'BH (BAHRAIN)',
    'BD' => 'BD (BANGLADESH)',
    'BB' => 'BB (BARBADOS)',
    'BY' => 'BY (BELARUS)',
    'BE' => 'BE (BELGIUM)',
    'BZ' => 'BZ (BELIZE)',
    'BJ' => 'BJ (BENIN)',
    'BM' => 'BM (BERMUDA)',
    'BT' => 'BT (BHUTAN)',
    'BO' => 'BO (BOLIVIA, PLURINATIONAL STATE OF)',
    'BA' => 'BA (BOSNIA AND HERZEGOVINA)',
    'BW' => 'BW (BOTSWANA)',
    'BV' => 'BV (BOUVET ISLAND)',
    'BR' => 'BR (BRAZIL)',
    'IO' => 'IO (BRITISH INDIAN OCEAN TERRITORY)',
    'BN' => 'BN (BRUNEI DARUSSALAM)',
    'BG' => 'BG (BULGARIA)',
    'BF' => 'BF (BURKINA FASO)',
    'BI' => 'BI (BURUNDI)',
    'KH' => 'KH (CAMBODIA)',
    'CM' => 'CM (CAMEROON)',
    'CA' => 'CA (CANADA)',
    'CV' => 'CV (CAPE VERDE)',
    'KY' => 'KY (CAYMAN ISLANDS)',
    'CF' => 'CF (CENTRAL AFRICAN REPUBLIC)',
    'TD' => 'TD (CHAD)',
    'CL' => 'CL (CHILE)',
    'CN' => 'CN (CHINA)',
    'CX' => 'CX (CHRISTMAS ISLAND)',
    'CC' => 'CC (COCOS (KEELING) ISLANDS)',
    'CO' => 'CO (COLOMBIA)',
    'KM' => 'KM (COMOROS)',
    'CG' => 'CG (CONGO)',
    'CD' => 'CD (CONGO, THE DEMOCRATIC REPUBLIC OF THE)',
    'CK' => 'CK (COOK ISLANDS)',
    'CR' => 'CR (COSTA RICA)',
    'CI' => 'CI (COTE D\'IVOIRE)',
    'HR' => 'HR (CROATIA)',
    'CU' => 'CU (CUBA)',
    'CY' => 'CY (CYPRUS)',
    'CZ' => 'CZ (CZECH REPUBLIC)',
    'DK' => 'DK (DENMARK)',
    'DJ' => 'DJ (DJIBOUTI)',
    'DM' => 'DM (DOMINICA)',
    'DO' => 'DO (DOMINICAN REPUBLIC)',
    'EC' => 'EC (ECUADOR)',
    'EG' => 'EG (EGYPT)',
    'SV' => 'SV (EL SALVADOR)',
    'GQ' => 'GQ (EQUATORIAL GUINEA)',
    'ER' => 'ER (ERITREA)',
    'EE' => 'EE (ESTONIA)',
    'ET' => 'ET (ETHIOPIA)',
    'FK' => 'FK (FALKLAND ISLANDS (MALVINAS))',
    'FO' => 'FO (FAROE ISLANDS)',
    'FJ' => 'FJ (FIJI)',
    'FI' => 'FI (FINLAND)',
    'FR' => 'FR (FRANCE)',
    'GF' => 'GF (FRENCH GUIANA)',
    'PF' => 'PF (FRENCH POLYNESIA)',
    'TF' => 'TF (FRENCH SOUTHERN TERRITORIES)',
    'GA' => 'GA (GABON)',
    'GM' => 'GM (GAMBIA)',
    'GE' => 'GE (GEORGIA)',
    'DE' => 'DE (GERMANY)',
    'GH' => 'GH (GHANA)',
    'GI' => 'GI (GIBRALTAR)',
    'GR' => 'GR (GREECE)',
    'GL' => 'GL (GREENLAND)',
    'GD' => 'GD (GRENADA)',
    'GP' => 'GP (GUADELOUPE)',
    'GU' => 'GU (GUAM)',
    'GT' => 'GT (GUATEMALA)',
    'GG' => 'GG (GUERNSEY)',
    'GN' => 'GN (GUINEA)',
    'GW' => 'GW (GUINEA-BISSAU)',
    'GY' => 'GY (GUYANA)',
    'HT' => 'HT (HAITI)',
    'HM' => 'HM (HEARD ISLAND AND MCDONALD ISLANDS)',
    'VA' => 'VA (HOLY SEE (VATICAN CITY STATE))',
    'HN' => 'HN (HONDURAS)',
    'HK' => 'HK (HONG KONG)',
    'HU' => 'HU (HUNGARY)',
    'IS' => 'IS (ICELAND)',
    'IN' => 'IN (INDIA)',
    'ID' => 'ID (INDONESIA)',
    'IR' => 'IR (IRAN, ISLAMIC REPUBLIC OF)',
    'IQ' => 'IQ (IRAQ)',
    'IE' => 'IE (IRELAND)',
    'IM' => 'IM (ISLE OF MAN)',
    'IL' => 'IL (ISRAEL)',
    'IT' => 'IT (ITALY)',
    'JM' => 'JM (JAMAICA)',
    'JP' => 'JP (JAPAN)',
    'JE' => 'JE (JERSEY)',
    'JO' => 'JO (JORDAN)',
    'KZ' => 'KZ (KAZAKHSTAN)',
    'KE' => 'KE (KENYA)',
    'KI' => 'KI (KIRIBATI)',
    'KP' => 'KP (KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF)',
    'KR' => 'KR (KOREA, REPUBLIC OF)',
    'KW' => 'KW (KUWAIT)',
    'KG' => 'KG (KYRGYZSTAN)',
    'LA' => 'LA (LAO PEOPLE\'S DEMOCRATIC REPUBLIC)',
    'LV' => 'LV (LATVIA)',
    'LB' => 'LB (LEBANON)',
    'LS' => 'LS (LESOTHO)',
    'LR' => 'LR (LIBERIA)',
    'LY' => 'LY (LIBYAN ARAB JAMAHIRIYA)',
    'LI' => 'LI (LIECHTENSTEIN)',
    'LT' => 'LT (LITHUANIA)',
    'LU' => 'LU (LUXEMBOURG)',
    'MO' => 'MO (MACAO)',
    'MK' => 'MK (MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF)',
    'MG' => 'MG (MADAGASCAR)',
    'MW' => 'MW (MALAWI)',
    'MY' => 'MY (MALAYSIA)',
    'MV' => 'MV (MALDIVES)',
    'ML' => 'ML (MALI)',
    'MT' => 'MT (MALTA)',
    'MH' => 'MH (MARSHALL ISLANDS)',
    'MQ' => 'MQ (MARTINIQUE)',
    'MR' => 'MR (MAURITANIA)',
    'MU' => 'MU (MAURITIUS)',
    'YT' => 'YT (MAYOTTE)',
    'MX' => 'MX (MEXICO)',
    'FM' => 'FM (MICRONESIA, FEDERATED STATES OF)',
    'MD' => 'MD (MOLDOVA, REPUBLIC OF)',
    'MC' => 'MC (MONACO)',
    'MN' => 'MN (MONGOLIA)',
    'ME' => 'ME (MONTENEGRO)',
    'MS' => 'MS (MONTSERRAT)',
    'MA' => 'MA (MOROCCO)',
    'MZ' => 'MZ (MOZAMBIQUE)',
    'MM' => 'MM (MYANMAR)',
    'NA' => 'NA (NAMIBIA)',
    'NR' => 'NR (NAURU)',
    'NP' => 'NP (NEPAL)',
    'NL' => 'NL (NETHERLANDS)',
    'AN' => 'AN (NETHERLANDS ANTILLES)',
    'NC' => 'NC (NEW CALEDONIA)',
    'NZ' => 'NZ (NEW ZEALAND)',
    'NI' => 'NI (NICARAGUA)',
    'NE' => 'NE (NIGER)',
    'NG' => 'NG (NIGERIA)',
    'NU' => 'NU (NIUE)',
    'NF' => 'NF (NORFOLK ISLAND)',
    'MP' => 'MP (NORTHERN MARIANA ISLANDS)',
    'NO' => 'NO (NORWAY)',
    'OM' => 'OM (OMAN)',
    'PK' => 'PK (PAKISTAN)',
    'PW' => 'PW (PALAU)',
    'PS' => 'PS (PALESTINIAN TERRITORY, OCCUPIED)',
    'PA' => 'PA (PANAMA)',
    'PG' => 'PG (PAPUA NEW GUINEA)',
    'PY' => 'PY (PARAGUAY)',
    'PE' => 'PE (PERU)',
    'PH' => 'PH (PHILIPPINES)',
    'PN' => 'PN (PITCAIRN)',
    'PL' => 'PL (POLAND)',
    'PT' => 'PT (PORTUGAL)',
    'PR' => 'PR (PUERTO RICO)',
    'QA' => 'QA (QATAR)',
    'RE' => 'RE (REUNION)',
    'RO' => 'RO (ROMANIA)',
    'RU' => 'RU (RUSSIAN FEDERATION)',
    'RW' => 'RW (RWANDA)',
    'BL' => 'BL (SAINT BARTHELEMY)',
    'SH' => 'SH (SAINT HELENA)',
    'KN' => 'KN (SAINT KITTS AND NEVIS)',
    'LC' => 'LC (SAINT LUCIA)',
    'MF' => 'MF (SAINT MARTIN)',
    'PM' => 'PM (SAINT PIERRE AND MIQUELON)',
    'VC' => 'VC (SAINT VINCENT AND THE GRENADINES)',
    'WS' => 'WS (SAMOA)',
    'SM' => 'SM (SAN MARINO)',
    'ST' => 'ST (SAO TOME AND PRINCIPE)',
    'SA' => 'SA (SAUDI ARABIA)',
    'SN' => 'SN (SENEGAL)',
    'RS' => 'RS (SERBIA)',
    'SC' => 'SC (SEYCHELLES)',
    'SL' => 'SL (SIERRA LEONE)',
    'SG' => 'SG (SINGAPORE)',
    'SK' => 'SK (SLOVAKIA)',
    'SI' => 'SI (SLOVENIA)',
    'SB' => 'SB (SOLOMON ISLANDS)',
    'SO' => 'SO (SOMALIA)',
    'ZA' => 'ZA (SOUTH AFRICA)',
    'GS' => 'GS (SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS)',
    'ES' => 'ES (SPAIN)',
    'LK' => 'LK (SRI LANKA)',
    'SD' => 'SD (SUDAN)',
    'SR' => 'SR (SURINAME)',
    'SJ' => 'SJ (SVALBARD AND JAN MAYEN)',
    'SZ' => 'SZ (SWAZILAND)',
    'SE' => 'SE (SWEDEN)',
    'CH' => 'CH (SWITZERLAND)',
    'SY' => 'SY (SYRIAN ARAB REPUBLIC)',
    'TW' => 'TW (TAIWAN, PROVINCE OF CHINA)',
    'TJ' => 'TJ (TAJIKISTAN)',
    'TZ' => 'TZ (TANZANIA, UNITED REPUBLIC OF)',
    'TH' => 'TH (THAILAND)',
    'TL' => 'TL (TIMOR-LESTE)',
    'TG' => 'TG (TOGO)',
    'TK' => 'TK (TOKELAU)',
    'TO' => 'TO (TONGA)',
    'TT' => 'TT (TRINIDAD AND TOBAGO)',
    'TN' => 'TN (TUNISIA)',
    'TR' => 'TR (TURKEY)',
    'TM' => 'TM (TURKMENISTAN)',
    'TC' => 'TC (TURKS AND CAICOS ISLANDS)',
    'TV' => 'TV (TUVALU)',
    'UG' => 'UG (UGANDA)',
    'UA' => 'UA (UKRAINE)',
    'AE' => 'AE (UNITED ARAB EMIRATES)',
    'GB' => 'GB (UNITED KINGDOM)',
    'US' => 'US (UNITED STATES)',
    'UM' => 'UM (UNITED STATES MINOR OUTLYING ISLANDS)',
    'UY' => 'UY (URUGUAY)',
    'UZ' => 'UZ (UZBEKISTAN)',
    'VU' => 'VU (VANUATU)',
    'VE' => 'VE (VENEZUELA, BOLIVARIAN REPUBLIC OF)',
    'VN' => 'VN (VIET NAM)',
    'VG' => 'VG (VIRGIN ISLANDS, BRITISH)',
    'VI' => 'VI (VIRGIN ISLANDS, U.S.)',
    'WF' => 'WF (WALLIS AND FUTUNA)',
    'EH' => 'EH (WESTERN SAHARA)',
    'YE' => 'YE (YEMEN)',
    'ZM' => 'ZM (ZAMBIA)',
    'ZW' => 'ZW (ZIMBABWE)',
    'OTHER' => "- other -"
  );

  $about=array (
    'reg_vmusician'     => 'Musician or composer',
    'reg_vdj'           => 'DJ',
    'reg_vswdeveloper'  => 'Software Developer',
    'reg_vhwdeveloper'  => 'Hardware Developer',
    'reg_vswuser'       => 'Software User',
//  'reg_vmediapro'     => 'Media Professional',
    'reg_vmproducer'    => 'Music Producer',
    'reg_vvproducer'    => 'Video Producer',
    'reg_vresearcher'   => 'Researcher',
    'reg_vpress'        => 'Press',
    'reg_vinterested'   => 'Just interested',
    'reg_vother'        => 'Other'
  );
?>




<form action="index.php" method="post">

<fieldset class="fs">
  <input name="page" type="hidden" value="<?php echo $page;?>"/>
  <legend>Personalia:</legend>
  <label class="la" for="reg_name"><span class="error">*</span>Family Name:</label>
  <input id="reg_name" name="reg_name" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_name'])) echo $_POST['reg_name'];?>"/>
  <br />
  <label class="la" for="reg_prename"><span class="error">*</span>Given Name(s):</label>
  <input id="reg_prename" name="reg_prename" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_prename'])) echo $_POST['reg_prename'];?>"/>
  <br />
  <label class="la" for="reg_tagline">Tagline <small>(Affiliation, Company, Pseudonym,&hellip;)</small>:</label>
  <input id="reg_tagline" name="reg_tagline" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_tagline'])) echo $_POST['reg_tagline'];?>"/>
  <br/>
  <label class="ls">Note: The tagline will appear with your name on the badge.</label>
  <br/>
  <label class="la" for="reg_email"><span class="error">*</span>E-Mail address:</label>
  <input id="reg_email" name="reg_email" type="text" size="50" maxlength="100" value="<?php if (isset($_POST['reg_email'])) echo $_POST['reg_email'];?>"/>
  <br/>
  <label class="la" for="reg_country"><span class="error">*</span>Country:</label>
  <select name="reg_country" id="reg_country" size="1">
<?php gen_options($ctry, 'reg_country'); ?>
  </select>
  <br/>
  <input name="reg_email_confirm" type="text" size="50" maxlength="100" class="fx" value=""/>
  <label class="la" for="reg_agegroup">Age:</label>
  <select name="reg_agegroup" id="reg_agegroup" size="1">
<?php gen_options($ages, 'reg_agegroup'); ?>
  </select>
  <br/>
</fieldset>

<fieldset class="fa">
<legend>Conference specific:</legend>
  <label>There will be conference proceedings
  available for a nominal fee of about $20.</label><br/>
  <div class="la"><label class="la"><span class="error">*</span>Are you interested in buying a copy?</label></div>
  <div class="ra">
    <label><input type="radio" name="reg_proceedings" value="0"<?php _ck('reg_proceedings',0);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_proceedings" value="1"<?php _ck('reg_proceedings',1);?>/>Yes</label>
	</div>
	<br/>
  <label>Allow public listing of your name and affiliation in the "Who else is coming" list.</label><br/>
  <div class="la"><label class="la"><span class="error">*</span>Include me?</label></div>
  <div class="ra">
    <label><input type="radio" name="reg_whoelselist" value="0"<?php _ck('reg_whoelselist',0);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_whoelselist" value="1"<?php _ck('reg_whoelselist',1);?>/>Yes</label>
  </div>
</fieldset>

<fieldset class="fa">
  <legend>About yourself:</legend>
  <div class="la"><label class="la">You are... (multiple checks are ok):</label></div>
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
<?php gen_checktd($about); ?>
    </tr>
  </table>
  <div class="la"><label class="la">Profession:</label></div>
  <div>
  <span>
  <label><input type="radio" name="reg_profession" value="Pupil"<?php _ck('reg_profession','Pupil');?>/>Pupil</label>
  <label><input type="radio" name="reg_profession" value="Student"<?php _ck('reg_profession','Student');?>/>Student</label>
  <label><input type="radio" name="reg_profession" value="Employed"<?php _ck('reg_profession','Employed');?>/>Employed</label>
  <label><input type="radio" name="reg_profession" value="Freelance"<?php _ck('reg_profession','Freelance');?>/>Freelance</label>
  <label><input type="radio" name="reg_profession" value="Other"<?php _ck('reg_profession','Other');?>/>Other</label>
  </span>
  </div>
  <div class="la"><label class="la">Do you work for a professional audio company?</label></div>
  <div class="rb">
  <span>
    <label><input type="radio" name="reg_audiopro" value="1"<?php _ck('reg_audiopro',1);?>/>No</label> &nbsp; &nbsp;
    <label><input type="radio" name="reg_audiopro" value="2"<?php _ck('reg_audiopro',2);?>/>Yes</label>
  </span>
  </div>
  <div class="la"><label class="la">You are using Linux..</label></div>
  <div class="ra">
   <span>
    <label><input type="checkbox" name="reg_useathome" value="1"<?php _ck('reg_useathome',1);?>/>..at home.</label> &nbsp; &nbsp;
    <label><input type="checkbox" name="reg_useatwork" value="1"<?php _ck('reg_useatwork',1);?>/>..at work.</label>
   </span>
  </div>
</fieldset>

<fieldset class="fs">
<legend>Miscellaneous:</legend>
  <label class="la" for="reg_notes">Remarks:</label>
  <textarea id="reg_notes" name="reg_notes" rows="3" cols="60"><?php if (isset($_POST['reg_notes'])) echo $_POST['reg_notes'];?></textarea><br/>
</fieldset>
<div>
  <div style="float:right;"><input type="reset" value="Reset fields"/></div>
  <div><input type="submit" value="Register Now!"/></div>
</div>

</form>
</div>

