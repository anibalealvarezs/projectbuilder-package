<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbLanguage as Language;
use Illuminate\Database\Seeder;

class PbLanguagesSeeder extends Seeder
{
    public static function byPass()
    {
        //
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Language::count() == 0) {
            // Default Languages
            Language::updateOrCreate(['code' => 'ab'], ['name' => 'Abkhazian']);
            Language::updateOrCreate(['code' => 'aa'], ['name' => 'Afar']);
            Language::updateOrCreate(['code' => 'af'], ['name' => 'Afrikaans']);
            Language::updateOrCreate(['code' => 'ak'], ['name' => 'Akan']);
            Language::updateOrCreate(['code' => 'sq'], ['name' => 'Albanian']);
            Language::updateOrCreate(['code' => 'am'], ['name' => 'Amharic']);
            Language::updateOrCreate(['code' => 'ar'], ['name' => 'Arabic']);
            Language::updateOrCreate(['code' => 'an'], ['name' => 'Aragonese']);
            Language::updateOrCreate(['code' => 'hy'], ['name' => 'Armenian']);
            Language::updateOrCreate(['code' => 'as'], ['name' => 'Assamese']);
            Language::updateOrCreate(['code' => 'av'], ['name' => 'Avaric']);
            Language::updateOrCreate(['code' => 'ae'], ['name' => 'Avestan']);
            Language::updateOrCreate(['code' => 'ay'], ['name' => 'Aymara']);
            Language::updateOrCreate(['code' => 'az'], ['name' => 'Azerbaijani']);
            Language::updateOrCreate(['code' => 'bm'], ['name' => 'Bambara']);
            Language::updateOrCreate(['code' => 'ba'], ['name' => 'Bashkir']);
            Language::updateOrCreate(['code' => 'eu'], ['name' => 'Basque']);
            Language::updateOrCreate(['code' => 'be'], ['name' => 'Belarusian']);
            Language::updateOrCreate(['code' => 'bn'], ['name' => 'Bengali']);
            Language::updateOrCreate(['code' => 'bh'], ['name' => 'Bihari languages']);
            Language::updateOrCreate(['code' => 'bi'], ['name' => 'Bislama']);
            Language::updateOrCreate(['code' => 'bs'], ['name' => 'Bosnian']);
            Language::updateOrCreate(['code' => 'br'], ['name' => 'Breton']);
            Language::updateOrCreate(['code' => 'bg'], ['name' => 'Bulgarian']);
            Language::updateOrCreate(['code' => 'my'], ['name' => 'Burmese']);
            Language::updateOrCreate(['code' => 'ca'], ['name' => 'Catalan, Valencian']);
            Language::updateOrCreate(['code' => 'ch'], ['name' => 'Chamorro']);
            Language::updateOrCreate(['code' => 'ce'], ['name' => 'Chechen']);
            Language::updateOrCreate(['code' => 'ny'], ['name' => 'Chichewa, Chewa, Nyanja']);
            Language::updateOrCreate(['code' => 'zh'], ['name' => 'Chinese']);
            Language::updateOrCreate(['code' => 'cv'], ['name' => 'Chuvash']);
            Language::updateOrCreate(['code' => 'kw'], ['name' => 'Cornish']);
            Language::updateOrCreate(['code' => 'co'], ['name' => 'Corsican']);
            Language::updateOrCreate(['code' => 'cr'], ['name' => 'Cree']);
            Language::updateOrCreate(['code' => 'hr'], ['name' => 'Croatian']);
            Language::updateOrCreate(['code' => 'cs'], ['name' => 'Czech']);
            Language::updateOrCreate(['code' => 'da'], ['name' => 'Danish']);
            Language::updateOrCreate(['code' => 'dv'], ['name' => 'Divehi, Dhivehi, Maldivian']);
            Language::updateOrCreate(['code' => 'nl'], ['name' => 'Dutch, Flemish']);
            Language::updateOrCreate(['code' => 'dz'], ['name' => 'Dzongkha']);
            Language::updateOrCreate(['code' => 'en'], ['name' => 'English', 'status' => 1]);
            Language::updateOrCreate(['code' => 'eo'], ['name' => 'Esperanto']);
            Language::updateOrCreate(['code' => 'et'], ['name' => 'Estonian']);
            Language::updateOrCreate(['code' => 'ee'], ['name' => 'Ewe']);
            Language::updateOrCreate(['code' => 'fo'], ['name' => 'Faroese']);
            Language::updateOrCreate(['code' => 'fj'], ['name' => 'Fijian']);
            Language::updateOrCreate(['code' => 'fi'], ['name' => 'Finnish']);
            Language::updateOrCreate(['code' => 'fr'], ['name' => 'French']);
            Language::updateOrCreate(['code' => 'ff'], ['name' => 'Fulah']);
            Language::updateOrCreate(['code' => 'gl'], ['name' => 'Galician']);
            Language::updateOrCreate(['code' => 'ka'], ['name' => 'Georgian']);
            Language::updateOrCreate(['code' => 'de'], ['name' => 'German']);
            Language::updateOrCreate(['code' => 'el'], ['name' => 'Greek (modern)']);
            Language::updateOrCreate(['code' => 'gn'], ['name' => 'Guaraní']);
            Language::updateOrCreate(['code' => 'gu'], ['name' => 'Gujarati']);
            Language::updateOrCreate(['code' => 'ht'], ['name' => 'Haitian, Haitian Creole']);
            Language::updateOrCreate(['code' => 'ha'], ['name' => 'Hausa']);
            Language::updateOrCreate(['code' => 'he'], ['name' => 'Hebrew (modern)']);
            Language::updateOrCreate(['code' => 'hz'], ['name' => 'Herero']);
            Language::updateOrCreate(['code' => 'hi'], ['name' => 'Hindi']);
            Language::updateOrCreate(['code' => 'ho'], ['name' => 'Hiri Motu']);
            Language::updateOrCreate(['code' => 'hu'], ['name' => 'Hungarian']);
            Language::updateOrCreate(['code' => 'ia'], ['name' => 'Interlingua']);
            Language::updateOrCreate(['code' => 'id'], ['name' => 'Indonesian']);
            Language::updateOrCreate(['code' => 'ie'], ['name' => 'Interlingue']);
            Language::updateOrCreate(['code' => 'ga'], ['name' => 'Irish']);
            Language::updateOrCreate(['code' => 'ig'], ['name' => 'Igbo']);
            Language::updateOrCreate(['code' => 'ik'], ['name' => 'Inupiaq']);
            Language::updateOrCreate(['code' => 'io'], ['name' => 'Ido']);
            Language::updateOrCreate(['code' => 'is'], ['name' => 'Icelandic']);
            Language::updateOrCreate(['code' => 'it'], ['name' => 'Italian']);
            Language::updateOrCreate(['code' => 'iu'], ['name' => 'Inuktitut']);
            Language::updateOrCreate(['code' => 'ja'], ['name' => 'Japanese']);
            Language::updateOrCreate(['code' => 'jv'], ['name' => 'Javanese']);
            Language::updateOrCreate(['code' => 'kl'], ['name' => 'Kalaallisut, Greenlandic']);
            Language::updateOrCreate(['code' => 'kn'], ['name' => 'Kannada']);
            Language::updateOrCreate(['code' => 'kr'], ['name' => 'Kanuri']);
            Language::updateOrCreate(['code' => 'ks'], ['name' => 'Kashmiri']);
            Language::updateOrCreate(['code' => 'kk'], ['name' => 'Kazakh']);
            Language::updateOrCreate(['code' => 'km'], ['name' => 'Central Khmer']);
            Language::updateOrCreate(['code' => 'ki'], ['name' => 'Kikuyu, Gikuyu']);
            Language::updateOrCreate(['code' => 'rw'], ['name' => 'Kinyarwanda']);
            Language::updateOrCreate(['code' => 'ky'], ['name' => 'Kirghiz, Kyrgyz']);
            Language::updateOrCreate(['code' => 'kv'], ['name' => 'Komi']);
            Language::updateOrCreate(['code' => 'kg'], ['name' => 'Kongo']);
            Language::updateOrCreate(['code' => 'ko'], ['name' => 'Korean']);
            Language::updateOrCreate(['code' => 'ku'], ['name' => 'Kurdish']);
            Language::updateOrCreate(['code' => 'kj'], ['name' => 'Kuanyama, Kwanyama']);
            Language::updateOrCreate(['code' => 'la'], ['name' => 'Latin']);
            Language::updateOrCreate(['code' => 'lb'], ['name' => 'Luxembourgish, Letzeburgesch']);
            Language::updateOrCreate(['code' => 'lg'], ['name' => 'Ganda']);
            Language::updateOrCreate(['code' => 'li'], ['name' => 'Limburgan, Limburger, Limburgish']);
            Language::updateOrCreate(['code' => 'ln'], ['name' => 'Lingala']);
            Language::updateOrCreate(['code' => 'lo'], ['name' => 'Lao']);
            Language::updateOrCreate(['code' => 'lt'], ['name' => 'Lithuanian']);
            Language::updateOrCreate(['code' => 'lu'], ['name' => 'Luba-Katanga']);
            Language::updateOrCreate(['code' => 'lv'], ['name' => 'Latvian']);
            Language::updateOrCreate(['code' => 'gv'], ['name' => 'Manx']);
            Language::updateOrCreate(['code' => 'mk'], ['name' => 'Macedonian']);
            Language::updateOrCreate(['code' => 'mg'], ['name' => 'Malagasy']);
            Language::updateOrCreate(['code' => 'ms'], ['name' => 'Malay']);
            Language::updateOrCreate(['code' => 'ml'], ['name' => 'Malayalam']);
            Language::updateOrCreate(['code' => 'mt'], ['name' => 'Maltese']);
            Language::updateOrCreate(['code' => 'mi'], ['name' => 'Maori']);
            Language::updateOrCreate(['code' => 'mr'], ['name' => 'Marathi']);
            Language::updateOrCreate(['code' => 'mh'], ['name' => 'Marshallese']);
            Language::updateOrCreate(['code' => 'mn'], ['name' => 'Mongolian']);
            Language::updateOrCreate(['code' => 'na'], ['name' => 'Nauru']);
            Language::updateOrCreate(['code' => 'nv'], ['name' => 'Navajo, Navaho']);
            Language::updateOrCreate(['code' => 'nd'], ['name' => 'North Ndebele']);
            Language::updateOrCreate(['code' => 'ne'], ['name' => 'Nepali']);
            Language::updateOrCreate(['code' => 'ng'], ['name' => 'Ndonga']);
            Language::updateOrCreate(['code' => 'nb'], ['name' => 'Norwegian Bokmål']);
            Language::updateOrCreate(['code' => 'nn'], ['name' => 'Norwegian Nynorsk']);
            Language::updateOrCreate(['code' => 'no'], ['name' => 'Norwegian']);
            Language::updateOrCreate(['code' => 'ii'], ['name' => 'Sichuan Yi, Nuosu']);
            Language::updateOrCreate(['code' => 'nr'], ['name' => 'South Ndebele']);
            Language::updateOrCreate(['code' => 'oc'], ['name' => 'Occitan']);
            Language::updateOrCreate(['code' => 'oj'], ['name' => 'Ojibwa']);
            Language::updateOrCreate(['code' => 'cu'], ['name' => 'Church Slavic, Church Slavonic, Old Church Slavonic, Old Slavonic, Old Bulgarian']);
            Language::updateOrCreate(['code' => 'om'], ['name' => 'Oromo']);
            Language::updateOrCreate(['code' => 'or'], ['name' => 'Oriya']);
            Language::updateOrCreate(['code' => 'os'], ['name' => 'Ossetian, Ossetic']);
            Language::updateOrCreate(['code' => 'pa'], ['name' => 'Panjabi, Punjabi']);
            Language::updateOrCreate(['code' => 'pi'], ['name' => 'Pali']);
            Language::updateOrCreate(['code' => 'fa'], ['name' => 'Persian']);
            Language::updateOrCreate(['code' => 'po'], ['name' => 'Polabian']);
            Language::updateOrCreate(['code' => 'pl'], ['name' => 'Polish']);
            Language::updateOrCreate(['code' => 'ps'], ['name' => 'Pashto, Pushto']);
            Language::updateOrCreate(['code' => 'pt'], ['name' => 'Portuguese']);
            Language::updateOrCreate(['code' => 'qu'], ['name' => 'Quechua']);
            Language::updateOrCreate(['code' => 'rm'], ['name' => 'Romansh']);
            Language::updateOrCreate(['code' => 'rn'], ['name' => 'Rundi']);
            Language::updateOrCreate(['code' => 'ro'], ['name' => 'Romanian, Moldavian, Moldovan']);
            Language::updateOrCreate(['code' => 'ru'], ['name' => 'Russian']);
            Language::updateOrCreate(['code' => 'sa'], ['name' => 'Sanskrit']);
            Language::updateOrCreate(['code' => 'sc'], ['name' => 'Sardinian']);
            Language::updateOrCreate(['code' => 'sd'], ['name' => 'Sindhi']);
            Language::updateOrCreate(['code' => 'se'], ['name' => 'Northern Sami']);
            Language::updateOrCreate(['code' => 'sm'], ['name' => 'Samoan']);
            Language::updateOrCreate(['code' => 'sg'], ['name' => 'Sango']);
            Language::updateOrCreate(['code' => 'sr'], ['name' => 'Serbian']);
            Language::updateOrCreate(['code' => 'gd'], ['name' => 'Gaelic, Scottish Gaelic']);
            Language::updateOrCreate(['code' => 'sn'], ['name' => 'Shona']);
            Language::updateOrCreate(['code' => 'si'], ['name' => 'Sinhala, Sinhalese']);
            Language::updateOrCreate(['code' => 'sk'], ['name' => 'Slovak']);
            Language::updateOrCreate(['code' => 'sl'], ['name' => 'Slovenian']);
            Language::updateOrCreate(['code' => 'so'], ['name' => 'Somali']);
            Language::updateOrCreate(['code' => 'st'], ['name' => 'Southern Sotho']);
            Language::updateOrCreate(['code' => 'es'], ['name' => 'Spanish, Castilian', 'status' => 1]);
            Language::updateOrCreate(['code' => 'su'], ['name' => 'Sundanese']);
            Language::updateOrCreate(['code' => 'sw'], ['name' => 'Swahili']);
            Language::updateOrCreate(['code' => 'ss'], ['name' => 'Swati']);
            Language::updateOrCreate(['code' => 'sv'], ['name' => 'Swedish']);
            Language::updateOrCreate(['code' => 'ta'], ['name' => 'Tamil']);
            Language::updateOrCreate(['code' => 'te'], ['name' => 'Telugu']);
            Language::updateOrCreate(['code' => 'tg'], ['name' => 'Tajik']);
            Language::updateOrCreate(['code' => 'th'], ['name' => 'Thai']);
            Language::updateOrCreate(['code' => 'ti'], ['name' => 'Tigrinya']);
            Language::updateOrCreate(['code' => 'bo'], ['name' => 'Tibetan']);
            Language::updateOrCreate(['code' => 'tk'], ['name' => 'Turkmen']);
            Language::updateOrCreate(['code' => 'tl'], ['name' => 'Tagalog']);
            Language::updateOrCreate(['code' => 'tn'], ['name' => 'Tswana']);
            Language::updateOrCreate(['code' => 'to'], ['name' => 'Tonga (Tonga Islands)']);
            Language::updateOrCreate(['code' => 'tr'], ['name' => 'Turkish']);
            Language::updateOrCreate(['code' => 'ts'], ['name' => 'Tsonga']);
            Language::updateOrCreate(['code' => 'tt'], ['name' => 'Tatar']);
            Language::updateOrCreate(['code' => 'tw'], ['name' => 'Twi']);
            Language::updateOrCreate(['code' => 'ty'], ['name' => 'Tahitian']);
            Language::updateOrCreate(['code' => 'ug'], ['name' => 'Uighur, Uyghur']);
            Language::updateOrCreate(['code' => 'uk'], ['name' => 'Ukrainian']);
            Language::updateOrCreate(['code' => 'ur'], ['name' => 'Urdu']);
            Language::updateOrCreate(['code' => 'uz'], ['name' => 'Uzbek']);
            Language::updateOrCreate(['code' => 've'], ['name' => 'Venda']);
            Language::updateOrCreate(['code' => 'vi'], ['name' => 'Vietnamese']);
            Language::updateOrCreate(['code' => 'vo'], ['name' => 'Volapük']);
            Language::updateOrCreate(['code' => 'wa'], ['name' => 'Walloon']);
            Language::updateOrCreate(['code' => 'cy'], ['name' => 'Welsh']);
            Language::updateOrCreate(['code' => 'wo'], ['name' => 'Wolof']);
            Language::updateOrCreate(['code' => 'fy'], ['name' => 'Western Frisian']);
            Language::updateOrCreate(['code' => 'xh'], ['name' => 'Xhosa']);
            Language::updateOrCreate(['code' => 'yi'], ['name' => 'Yiddish']);
            Language::updateOrCreate(['code' => 'yo'], ['name' => 'Yoruba']);
            Language::updateOrCreate(['code' => 'za'], ['name' => 'Zhuang, Chuang']);
            Language::updateOrCreate(['code' => 'zu'], ['name' => 'Zulu']);
        }
    }
}
