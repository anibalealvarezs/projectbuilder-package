<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbCity as City;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbCountry as Country;
use Illuminate\Database\Seeder;

class PbCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default Countries
        Country::upsert([
            ['code' => 'AF', 'name' => json_encode(['en' => 'Afghanistan', 'es' => 'Afganistán'])],
            ['code' => 'AX', 'name' => json_encode(['en' => 'Aland Islands', 'es' => 'Islas Aland'])],
            ['code' => 'AL', 'name' => json_encode(['en' => 'Albania', 'es' => 'Albania'])],
            ['code' => 'DZ', 'name' => json_encode(['en' => 'Algeria', 'es' => 'Algeria'])],
            ['code' => 'AS', 'name' => json_encode(['en' => 'American Samoa', 'es' => 'Samoa Americana'])],
            ['code' => 'AD', 'name' => json_encode(['en' => 'Andorra', 'es' => 'Andorra'])],
            ['code' => 'AO', 'name' => json_encode(['en' => 'Angola', 'es' => 'Angola'])],
            ['code' => 'AI', 'name' => json_encode(['en' => 'Anguilla', 'es' => 'Anguila'])],
            ['code' => 'AQ', 'name' => json_encode(['en' => 'Antarctica', 'es' => 'Antárctica'])],
            ['code' => 'AG', 'name' => json_encode(['en' => 'Antigua', 'es' => 'Antigua'])],
            ['code' => 'AR', 'name' => json_encode(['en' => 'Argentina', 'es' => 'Argentina'])],
            ['code' => 'AM', 'name' => json_encode(['en' => 'Armenia', 'es' => 'Armenia'])],
            ['code' => 'AW', 'name' => json_encode(['en' => 'Aruba', 'es' => 'Aruba'])],
            ['code' => 'AU', 'name' => json_encode(['en' => 'Australia', 'es' => 'Australia'])],
            ['code' => 'AT', 'name' => json_encode(['en' => 'Austria', 'es' => 'Austria'])],
            ['code' => 'AZ', 'name' => json_encode(['en' => 'Azerbaijan', 'es' => 'Azerbayán'])],
            ['code' => 'BH', 'name' => json_encode(['en' => 'Bahrain', 'es' => 'Bahrein'])],
            ['code' => 'BD', 'name' => json_encode(['en' => 'Bangladesh', 'es' => 'Bangladesh'])],
            ['code' => 'BB', 'name' => json_encode(['en' => 'Barbados', 'es' => 'Barbados'])],
            ['code' => 'BY', 'name' => json_encode(['en' => 'Belarus', 'es' => 'Bielorrusia'])],
            ['code' => 'BE', 'name' => json_encode(['en' => 'Belgium', 'es' => 'Bélgica'])],
            ['code' => 'BZ', 'name' => json_encode(['en' => 'Belize', 'es' => 'Bélice'])],
            ['code' => 'BJ', 'name' => json_encode(['en' => 'Benin', 'es' => 'Benín'])],
            ['code' => 'BM', 'name' => json_encode(['en' => 'Bermuda', 'es' => 'Bermuda'])],
            ['code' => 'BT', 'name' => json_encode(['en' => 'Bhutan', 'es' => 'Bután'])],
            ['code' => 'BO', 'name' => json_encode(['en' => 'Bolivia', 'es' => 'Bolivia'])],
            ['code' => 'BQ', 'name' => json_encode(['en' => 'Bonaire, Sint Eustatius and Saba', 'es' => 'Bonaire, San Eustaquio y Saba'])],
            ['code' => 'BA', 'name' => json_encode(['en' => 'Bosnia and Herzegovina', 'es' => 'Bosnia y Herzegovina'])],
            ['code' => 'BW', 'name' => json_encode(['en' => 'Botswana', 'es' => 'Botswana'])],
            ['code' => 'BV', 'name' => json_encode(['en' => 'Bouvet Island', 'es' => 'Isla Bouvet'])],
            ['code' => 'BR', 'name' => json_encode(['en' => 'Brazil', 'es' => 'Brasil'])],
            ['code' => 'IO', 'name' => json_encode(['en' => 'British Indian Ocean Territory', 'es' => 'Territorio Británico del Océano Índico'])],
            ['code' => 'BN', 'name' => json_encode(['en' => 'Brunei Darussalam', 'es' => 'Brunéi Darussalam'])],
            ['code' => 'BG', 'name' => json_encode(['en' => 'Bulgaria', 'es' => 'Bulgaria'])],
            ['code' => 'BF', 'name' => json_encode(['en' => 'Burkina Faso', 'es' => 'Burkina Faso'])],
            ['code' => 'BI', 'name' => json_encode(['en' => 'Burundi', 'es' => 'Burundi'])],
            ['code' => 'KH', 'name' => json_encode(['en' => 'Cambodia', 'es' => 'Camboya'])],
            ['code' => 'CM', 'name' => json_encode(['en' => 'Cameroon', 'es' => 'Camerún'])],
            ['code' => 'CA', 'name' => json_encode(['en' => 'Canada', 'es' => 'Canadá'])],
            ['code' => 'CV', 'name' => json_encode(['en' => 'Cape Verde', 'es' => 'Cabo Verde'])],
            ['code' => 'KY', 'name' => json_encode(['en' => 'Cayman Islands', 'es' => 'Islas Caimán'])],
            ['code' => 'CF', 'name' => json_encode(['en' => 'Central African Republic', 'es' => 'República Centroafricana'])],
            ['code' => 'TD', 'name' => json_encode(['en' => 'Chad', 'es' => 'Chad'])],
            ['code' => 'CL', 'name' => json_encode(['en' => 'Chile', 'es' => 'Chile'])],
            ['code' => 'CN', 'name' => json_encode(['en' => 'China', 'es' => 'China'])],
            ['code' => 'CX', 'name' => json_encode(['en' => 'Christmas Island', 'es' => 'Isla de Navidad'])],
            ['code' => 'CC', 'name' => json_encode(['en' => 'Cocos (Keeling) Islands', 'es' => 'Islas Cocos (Keeling)'])],
            ['code' => 'CO', 'name' => json_encode(['en' => 'Colombia', 'es' => 'Colombia'])],
            ['code' => 'KM', 'name' => json_encode(['en' => 'Comoros', 'es' => 'Comoras'])],
            ['code' => 'CK', 'name' => json_encode(['en' => 'Cook Islands', 'es' => 'Islas Cook'])],
            ['code' => 'CR', 'name' => json_encode(['en' => 'Costa Rica', 'es' => 'Costa Rica'])],
            ['code' => 'CI', 'name' => json_encode(['en' => 'Côte d\'Ivoire', 'es' => 'Costa de Marfil'])],
            ['code' => 'HR', 'name' => json_encode(['en' => 'Croatia', 'es' => 'Croacia'])],
            ['code' => 'CW', 'name' => json_encode(['en' => 'Curaçao', 'es' => 'Curazao'])],
            ['code' => 'CY', 'name' => json_encode(['en' => 'Cyprus', 'es' => 'Chipre'])],
            ['code' => 'CZ', 'name' => json_encode(['en' => 'Czech Republic', 'es' => 'República Checa'])],
            ['code' => 'CD', 'name' => json_encode(['en' => 'Democratic Republic of the Congo', 'es' => 'República Democrática del Congo'])],
            ['code' => 'DK', 'name' => json_encode(['en' => 'Denmark', 'es' => 'Dinamarca'])],
            ['code' => 'DJ', 'name' => json_encode(['en' => 'Djibouti', 'es' => 'Yibuti'])],
            ['code' => 'DM', 'name' => json_encode(['en' => 'Dominica', 'es' => 'Dominica'])],
            ['code' => 'DO', 'name' => json_encode(['en' => 'Dominican Republic', 'es' => 'República Dominicana'])],
            ['code' => 'EC', 'name' => json_encode(['en' => 'Ecuador', 'es' => 'Ecuador'])],
            ['code' => 'EG', 'name' => json_encode(['en' => 'Egypt', 'es' => 'Egipto'])],
            ['code' => 'SV', 'name' => json_encode(['en' => 'El Salvador', 'es' => 'El Salvador'])],
            ['code' => 'GQ', 'name' => json_encode(['en' => 'Equatorial Guinea', 'es' => 'Guinea Ecuatorial'])],
            ['code' => 'ER', 'name' => json_encode(['en' => 'Eritrea', 'es' => 'Eritrea'])],
            ['code' => 'EE', 'name' => json_encode(['en' => 'Estonia', 'es' => 'Estonia'])],
            ['code' => 'ET', 'name' => json_encode(['en' => 'Ethiopia', 'es' => 'Etiopía'])],
            ['code' => 'FK', 'name' => json_encode(['en' => 'Falkland Islands', 'es' => 'Islas Malvinas'])],
            ['code' => 'FO', 'name' => json_encode(['en' => 'Faroe Islands', 'es' => 'Islas Feroe'])],
            ['code' => 'FM', 'name' => json_encode(['en' => 'Micronesia', 'es' => 'Micronesia'])],
            ['code' => 'FJ', 'name' => json_encode(['en' => 'Fiji', 'es' => 'Fiyi'])],
            ['code' => 'FI', 'name' => json_encode(['en' => 'Finland', 'es' => 'Finlandia'])],
            ['code' => 'FR', 'name' => json_encode(['en' => 'France', 'es' => 'Francia'])],
            ['code' => 'GF', 'name' => json_encode(['en' => 'French Guiana', 'es' => 'Guayana Francesa'])],
            ['code' => 'PF', 'name' => json_encode(['en' => 'French Polynesia', 'es' => 'Polinesia Francesa'])],
            ['code' => 'TF', 'name' => json_encode(['en' => 'French Southern Territories', 'es' => 'Territorios Franceses del Sur'])],
            ['code' => 'GA', 'name' => json_encode(['en' => 'Gabon', 'es' => 'Gabón'])],
            ['code' => 'GE', 'name' => json_encode(['en' => 'Georgia', 'es' => 'Georgia'])],
            ['code' => 'DE', 'name' => json_encode(['en' => 'Germany', 'es' => 'Alemania'])],
            ['code' => 'GH', 'name' => json_encode(['en' => 'Ghana', 'es' => 'Ghana'])],
            ['code' => 'GI', 'name' => json_encode(['en' => 'Gibraltar', 'es' => 'Gibraltar'])],
            ['code' => 'GR', 'name' => json_encode(['en' => 'Greece', 'es' => 'Grecia'])],
            ['code' => 'GL', 'name' => json_encode(['en' => 'Greenland', 'es' => 'Groendlandia'])],
            ['code' => 'GD', 'name' => json_encode(['en' => 'Grenada', 'es' => 'Granada'])],
            ['code' => 'GP', 'name' => json_encode(['en' => 'Guadeloupe', 'es' => 'Guadalupe'])],
            ['code' => 'GU', 'name' => json_encode(['en' => 'Guam', 'es' => 'Guam'])],
            ['code' => 'GT', 'name' => json_encode(['en' => 'Guatemala', 'es' => 'Guatemala'])],
            ['code' => 'GG', 'name' => json_encode(['en' => 'Guernsey', 'es' => 'Guernesey'])],
            ['code' => 'GN', 'name' => json_encode(['en' => 'Guinea', 'es' => 'Guinea'])],
            ['code' => 'GW', 'name' => json_encode(['en' => 'Guinea-Bissau', 'es' => 'Guinea-Bisáu'])],
            ['code' => 'GY', 'name' => json_encode(['en' => 'Guyana', 'es' => 'Guyana'])],
            ['code' => 'HT', 'name' => json_encode(['en' => 'Haiti', 'es' => 'Haití'])],
            ['code' => 'HM', 'name' => json_encode(['en' => 'Heard Island and McDonald Islands', 'es' => 'Islas Heard y McDonald'])],
            ['code' => 'HN', 'name' => json_encode(['en' => 'Honduras', 'es' => 'Honduras'])],
            ['code' => 'HK', 'name' => json_encode(['en' => 'Hong Kong', 'es' => 'Hong Kong'])],
            ['code' => 'HU', 'name' => json_encode(['en' => 'Hungary', 'es' => 'Hungría'])],
            ['code' => 'IS', 'name' => json_encode(['en' => 'Iceland', 'es' => 'Islandia'])],
            ['code' => 'IN', 'name' => json_encode(['en' => 'India', 'es' => 'India'])],
            ['code' => 'ID', 'name' => json_encode(['en' => 'Indonesia', 'es' => 'Indonesia'])],
            ['code' => 'IQ', 'name' => json_encode(['en' => 'Iraq', 'es' => 'Irak'])],
            ['code' => 'IE', 'name' => json_encode(['en' => 'Ireland', 'es' => 'Irlanda'])],
            ['code' => 'IM', 'name' => json_encode(['en' => 'Isle of Man', 'es' => 'Isla de Man'])],
            ['code' => 'IL', 'name' => json_encode(['en' => 'Israel', 'es' => 'Israel'])],
            ['code' => 'IT', 'name' => json_encode(['en' => 'Italy', 'es' => 'Italia'])],
            ['code' => 'JM', 'name' => json_encode(['en' => 'Jamaica', 'es' => 'Jamaica'])],
            ['code' => 'JP', 'name' => json_encode(['en' => 'Japan', 'es' => 'Japón'])],
            ['code' => 'JE', 'name' => json_encode(['en' => 'Jersey', 'es' => 'Jersey'])],
            ['code' => 'JO', 'name' => json_encode(['en' => 'Jordan', 'es' => 'Jordania'])],
            ['code' => 'KZ', 'name' => json_encode(['en' => 'Kazakhstan', 'es' => 'Kazajstán'])],
            ['code' => 'KE', 'name' => json_encode(['en' => 'Kenya', 'es' => 'Kenia'])],
            ['code' => 'KI', 'name' => json_encode(['en' => 'Kiribati', 'es' => 'Kiribati'])],
            ['code' => 'XK', 'name' => json_encode(['en' => 'Kosovo', 'es' => 'Kosovo'])],
            ['code' => 'KW', 'name' => json_encode(['en' => 'Kuwait', 'es' => 'Kuwait'])],
            ['code' => 'KG', 'name' => json_encode(['en' => 'Kyrgyzstan', 'es' => 'Kirguistán'])],
            ['code' => 'LA', 'name' => json_encode(['en' => 'Laos', 'es' => 'Laos'])],
            ['code' => 'LV', 'name' => json_encode(['en' => 'Latvia', 'es' => 'Letonia'])],
            ['code' => 'LB', 'name' => json_encode(['en' => 'Lebanon', 'es' => 'Líbano'])],
            ['code' => 'LS', 'name' => json_encode(['en' => 'Lesotho', 'es' => 'Lesoto'])],
            ['code' => 'LR', 'name' => json_encode(['en' => 'Liberia', 'es' => 'Liberia'])],
            ['code' => 'LY', 'name' => json_encode(['en' => 'Libya', 'es' => 'Libia'])],
            ['code' => 'LI', 'name' => json_encode(['en' => 'Liechtenstein', 'es' => 'Liechtenstein'])],
            ['code' => 'LT', 'name' => json_encode(['en' => 'Lithuania', 'es' => 'Lituania'])],
            ['code' => 'LU', 'name' => json_encode(['en' => 'Luxembourg', 'es' => 'Luxemburgo'])],
            ['code' => 'MO', 'name' => json_encode(['en' => 'Macau', 'es' => 'Macao'])],
            ['code' => 'MK', 'name' => json_encode(['en' => 'Macedonia', 'es' => 'Macedonia'])],
            ['code' => 'MG', 'name' => json_encode(['en' => 'Madagascar', 'es' => 'Madagascar'])],
            ['code' => 'MW', 'name' => json_encode(['en' => 'Malawi', 'es' => 'Malaui'])],
            ['code' => 'MY', 'name' => json_encode(['en' => 'Malaysia', 'es' => 'Malasia'])],
            ['code' => 'MV', 'name' => json_encode(['en' => 'Maldives', 'es' => 'Maldivas'])],
            ['code' => 'ML', 'name' => json_encode(['en' => 'Mali', 'es' => 'Malí'])],
            ['code' => 'MT', 'name' => json_encode(['en' => 'Malta', 'es' => 'Malta'])],
            ['code' => 'MH', 'name' => json_encode(['en' => 'Marshall Islands', 'es' => 'Islas Marshall'])],
            ['code' => 'MQ', 'name' => json_encode(['en' => 'Martinique', 'es' => 'Matinica'])],
            ['code' => 'MR', 'name' => json_encode(['en' => 'Mauritania', 'es' => 'Mauritania'])],
            ['code' => 'MU', 'name' => json_encode(['en' => 'Mauritius', 'es' => 'Mauricio'])],
            ['code' => 'YT', 'name' => json_encode(['en' => 'Mayotte', 'es' => 'Mayotte'])],
            ['code' => 'MX', 'name' => json_encode(['en' => 'Mexico', 'es' => 'México'])],
            ['code' => 'MD', 'name' => json_encode(['en' => 'Moldova', 'es' => 'Moldavia'])],
            ['code' => 'MC', 'name' => json_encode(['en' => 'Monaco', 'es' => 'Mónaco'])],
            ['code' => 'MN', 'name' => json_encode(['en' => 'Mongolia', 'es' => 'Mongolia'])],
            ['code' => 'ME', 'name' => json_encode(['en' => 'Montenegro', 'es' => 'Montenegro'])],
            ['code' => 'MS', 'name' => json_encode(['en' => 'Montserrat', 'es' => 'Montserrat'])],
            ['code' => 'MA', 'name' => json_encode(['en' => 'Morocco', 'es' => 'Marruecos'])],
            ['code' => 'MZ', 'name' => json_encode(['en' => 'Mozambique', 'es' => 'Mozambique'])],
            ['code' => 'MM', 'name' => json_encode(['en' => 'Myanmar', 'es' => 'Myanmar'])],
            ['code' => 'NA', 'name' => json_encode(['en' => 'Namibia', 'es' => 'Namibia'])],
            ['code' => 'NR', 'name' => json_encode(['en' => 'Nauru', 'es' => 'Nauru'])],
            ['code' => 'NP', 'name' => json_encode(['en' => 'Nepal', 'es' => 'Nepal'])],
            ['code' => 'NL', 'name' => json_encode(['en' => 'Netherlands', 'es' => 'Holanda'])],
            ['code' => 'AN', 'name' => json_encode(['en' => 'Netherlands Antilles', 'es' => 'Antillas Holandesas'])],
            ['code' => 'NC', 'name' => json_encode(['en' => 'New Caledonia', 'es' => 'Nueva Caledonia'])],
            ['code' => 'NZ', 'name' => json_encode(['en' => 'New Zealand', 'es' => 'Nueva Zelanda'])],
            ['code' => 'NI', 'name' => json_encode(['en' => 'Nicaragua', 'es' => 'Nicaragua'])],
            ['code' => 'NE', 'name' => json_encode(['en' => 'Niger', 'es' => 'Níger'])],
            ['code' => 'NG', 'name' => json_encode(['en' => 'Nigeria', 'es' => 'Nigeria'])],
            ['code' => 'NU', 'name' => json_encode(['en' => 'Niue', 'es' => 'Niue'])],
            ['code' => 'NF', 'name' => json_encode(['en' => 'Norfolk Island', 'es' => 'Isla Norfolk'])],
            ['code' => 'MP', 'name' => json_encode(['en' => 'Northern Mariana Islands', 'es' => 'Islas Marianas del Norte'])],
            ['code' => 'NO', 'name' => json_encode(['en' => 'Norway', 'es' => 'Noruega'])],
            ['code' => 'OM', 'name' => json_encode(['en' => 'Oman', 'es' => 'Omán'])],
            ['code' => 'PK', 'name' => json_encode(['en' => 'Pakistan', 'es' => 'Pakistán'])],
            ['code' => 'PW', 'name' => json_encode(['en' => 'Palau', 'es' => 'Palaos'])],
            ['code' => 'PS', 'name' => json_encode(['en' => 'Palestine', 'es' => 'Palestina'])],
            ['code' => 'PA', 'name' => json_encode(['en' => 'Panama', 'es' => 'Panamá'])],
            ['code' => 'PG', 'name' => json_encode(['en' => 'Papua New Guinea', 'es' => 'Papúa Nueva Guinea'])],
            ['code' => 'PY', 'name' => json_encode(['en' => 'Paraguay', 'es' => 'Paraguay'])],
            ['code' => 'PE', 'name' => json_encode(['en' => 'Peru', 'es' => 'Perú'])],
            ['code' => 'PH', 'name' => json_encode(['en' => 'Philippines', 'es' => 'Filipinas'])],
            ['code' => 'PN', 'name' => json_encode(['en' => 'Pitcairn', 'es' => 'Pitcairn'])],
            ['code' => 'PL', 'name' => json_encode(['en' => 'Poland', 'es' => 'Polonia'])],
            ['code' => 'PT', 'name' => json_encode(['en' => 'Portugal', 'es' => 'Portugal'])],
            ['code' => 'PR', 'name' => json_encode(['en' => 'Puerto Rico', 'es' => 'Puerto Rico'])],
            ['code' => 'QA', 'name' => json_encode(['en' => 'Qatar', 'es' => 'Katar'])],
            ['code' => 'CG', 'name' => json_encode(['en' => 'Republic of the Congo', 'es' => 'República del Congo'])],
            ['code' => 'RO', 'name' => json_encode(['en' => 'Romania', 'es' => 'Rumania'])],
            ['code' => 'RU', 'name' => json_encode(['en' => 'Russia', 'es' => 'Rusia'])],
            ['code' => 'RW', 'name' => json_encode(['en' => 'Rwanda', 'es' => 'Ruanda'])],
            ['code' => 'RE', 'name' => json_encode(['en' => 'Réunion', 'es' => 'Reunión'])],
            ['code' => 'BL', 'name' => json_encode(['en' => 'Saint Barthélemy', 'es' => 'San Bartolomeo'])],
            ['code' => 'SH', 'name' => json_encode(['en' => 'Saint Helena', 'es' => 'Santa Helena'])],
            ['code' => 'KN', 'name' => json_encode(['en' => 'Saint Kitts and Nevis', 'es' => 'San Cristóbal y Nieves'])],
            ['code' => 'MF', 'name' => json_encode(['en' => 'Saint Martin', 'es' => 'San Martín'])],
            ['code' => 'PM', 'name' => json_encode(['en' => 'Saint Pierre and Miquelon', 'es' => 'San Pedro y Miquelón'])],
            ['code' => 'VC', 'name' => json_encode(['en' => 'Saint Vincent and the Grenadines', 'es' => 'San Vicente y las Granadinas'])],
            ['code' => 'WS', 'name' => json_encode(['en' => 'Samoa', 'es' => 'Samoa'])],
            ['code' => 'SM', 'name' => json_encode(['en' => 'San Marino', 'es' => 'San Marino'])],
            ['code' => 'ST', 'name' => json_encode(['en' => 'Sao Tome and Principe', 'es' => 'Santo Tomé y Príncipe'])],
            ['code' => 'SA', 'name' => json_encode(['en' => 'Saudi Arabia', 'es' => 'Arabia Saudita'])],
            ['code' => 'SN', 'name' => json_encode(['en' => 'Senegal', 'es' => 'Senegal'])],
            ['code' => 'RS', 'name' => json_encode(['en' => 'Serbia', 'es' => 'Serbia'])],
            ['code' => 'SC', 'name' => json_encode(['en' => 'Seychelles', 'es' => 'Seychelles'])],
            ['code' => 'SL', 'name' => json_encode(['en' => 'Sierra Leone', 'es' => 'Sierra Leona'])],
            ['code' => 'SG', 'name' => json_encode(['en' => 'Singapore', 'es' => 'Singapur'])],
            ['code' => 'SX', 'name' => json_encode(['en' => 'Sint Maarten', 'es' => 'San Martín'])],
            ['code' => 'SK', 'name' => json_encode(['en' => 'Slovakia', 'es' => 'Eslovaquia'])],
            ['code' => 'SI', 'name' => json_encode(['en' => 'Slovenia', 'es' => 'Eslovenia'])],
            ['code' => 'SB', 'name' => json_encode(['en' => 'Solomon Islands', 'es' => 'Islas Salomón'])],
            ['code' => 'SO', 'name' => json_encode(['en' => 'Somalia', 'es' => 'Somalia'])],
            ['code' => 'ZA', 'name' => json_encode(['en' => 'South Africa', 'es' => 'Sudáfrica'])],
            ['code' => 'GS', 'name' => json_encode(['en' => 'South Georgia and the South Sandwich Islands', 'es' => 'Georgia del sur y las islas Sandwich del sur'])],
            ['code' => 'KR', 'name' => json_encode(['en' => 'South Korea', 'es' => 'Corea del Sur'])],
            ['code' => 'SS', 'name' => json_encode(['en' => 'South Sudan', 'es' => 'Sudán del Sur'])],
            ['code' => 'ES', 'name' => json_encode(['en' => 'Spain', 'es' => 'España'])],
            ['code' => 'LK', 'name' => json_encode(['en' => 'Sri Lanka', 'es' => 'Sri Lanka'])],
            ['code' => 'LC', 'name' => json_encode(['en' => 'St. Lucia', 'es' => 'Santa Lucía'])],
            ['code' => 'SR', 'name' => json_encode(['en' => 'Suriname', 'es' => 'Surinam'])],
            ['code' => 'SJ', 'name' => json_encode(['en' => 'Svalbard and Jan Mayen', 'es' => 'Svalbard y Jan Mayen'])],
            ['code' => 'SZ', 'name' => json_encode(['en' => 'Swaziland', 'es' => 'Suazilandia'])],
            ['code' => 'SE', 'name' => json_encode(['en' => 'Sweden', 'es' => 'Suecia'])],
            ['code' => 'CH', 'name' => json_encode(['en' => 'Switzerland', 'es' => 'Suiza'])],
            ['code' => 'TW', 'name' => json_encode(['en' => 'Taiwan', 'es' => 'Taiwán'])],
            ['code' => 'TJ', 'name' => json_encode(['en' => 'Tajikistan', 'es' => 'Tayikistán'])],
            ['code' => 'TZ', 'name' => json_encode(['en' => 'Tanzania', 'es' => 'Tanzania'])],
            ['code' => 'TH', 'name' => json_encode(['en' => 'Thailand', 'es' => 'Tailandia'])],
            ['code' => 'BS', 'name' => json_encode(['en' => 'Bahamas', 'es' => 'Bahamas'])],
            ['code' => 'GM', 'name' => json_encode(['en' => 'Gambia', 'es' => 'Gambia'])],
            ['code' => 'TL', 'name' => json_encode(['en' => 'Timor-Leste', 'es' => 'Timor Oriental'])],
            ['code' => 'TG', 'name' => json_encode(['en' => 'Togo', 'es' => 'Togo'])],
            ['code' => 'TK', 'name' => json_encode(['en' => 'Tokelau', 'es' => 'Tokelau'])],
            ['code' => 'TO', 'name' => json_encode(['en' => 'Tonga', 'es' => 'Tonga'])],
            ['code' => 'TT', 'name' => json_encode(['en' => 'Trinidad and Tobago', 'es' => 'Trinidad y Tobago'])],
            ['code' => 'TN', 'name' => json_encode(['en' => 'Tunisia', 'es' => 'Túnez'])],
            ['code' => 'TR', 'name' => json_encode(['en' => 'Turkey', 'es' => 'Turquía'])],
            ['code' => 'TM', 'name' => json_encode(['en' => 'Turkmenistan', 'es' => 'Turkmenistán'])],
            ['code' => 'TC', 'name' => json_encode(['en' => 'Turks and Caicos Islands', 'es' => 'Islas Turcas y Caicos'])],
            ['code' => 'TV', 'name' => json_encode(['en' => 'Tuvalu', 'es' => 'Tuvalu'])],
            ['code' => 'VI', 'name' => json_encode(['en' => 'US Virgin Islands', 'es' => 'Islas Vírgenes de EE.UU'])],
            ['code' => 'UG', 'name' => json_encode(['en' => 'Uganda', 'es' => 'Uganda'])],
            ['code' => 'UA', 'name' => json_encode(['en' => 'Ukraine', 'es' => 'Ucrania'])],
            ['code' => 'AE', 'name' => json_encode(['en' => 'United Arab Emirates', 'es' => 'Emiratos Árabes Unidos'])],
            ['code' => 'GB', 'name' => json_encode(['en' => 'United Kingdom', 'es' => 'Reino Unido'])],
            ['code' => 'US', 'name' => json_encode(['en' => 'United States', 'es' => 'Estados Unidos'])],
            ['code' => 'UM', 'name' => json_encode(['en' => 'United States Minor Outlying Islands', 'es' => 'Islas ultramarinas menores de los Estados Unidos'])],
            ['code' => 'UY', 'name' => json_encode(['en' => 'Uruguay', 'es' => 'Uruguay'])],
            ['code' => 'UZ', 'name' => json_encode(['en' => 'Uzbekistan', 'es' => 'Uzbekistán'])],
            ['code' => 'VU', 'name' => json_encode(['en' => 'Vanuatu', 'es' => 'Vanuatu'])],
            ['code' => 'VA', 'name' => json_encode(['en' => 'Vatican City', 'es' => 'Ciudad del Vaticano'])],
            ['code' => 'VE', 'name' => json_encode(['en' => 'Venezuela', 'es' => 'Venezuela'])],
            ['code' => 'VN', 'name' => json_encode(['en' => 'Vietnam', 'es' => 'Vietnam'])],
            ['code' => 'WF', 'name' => json_encode(['en' => 'Wallis and Futuna', 'es' => 'Wallis y Futuna'])],
            ['code' => 'EH', 'name' => json_encode(['en' => 'Western Sahara', 'es' => 'Sahara Occidental'])],
            ['code' => 'YE', 'name' => json_encode(['en' => 'Yemen', 'es' => 'Yemen'])],
            ['code' => 'ZM', 'name' => json_encode(['en' => 'Zambia', 'es' => 'Zambia'])],
            ['code' => 'ZW', 'name' => json_encode(['en' => 'Zimbabwe', 'es' => 'Zimbabue'])],
            ['code' => 'IR', 'name' => json_encode(['en' => 'Iran', 'es' => 'Irán'])],
            ['code' => 'CU', 'name' => json_encode(['en' => 'Cuba', 'es' => 'Cuba'])],
            ['code' => 'KP', 'name' => json_encode(['en' => 'North Korea', 'es' => 'Corea del Norte'])],
            ['code' => 'SY', 'name' => json_encode(['en' => 'Syria', 'es' => 'Siria'])],
            ['code' => 'SD', 'name' => json_encode(['en' => 'Sudan', 'es' => 'Sudán'])]
        ], ['code'], ['name']);
    }
}
