<!-- NOTE: SOME DATA ARE NOT IN THE TEMPLATE, THIS STILL NEEDS IMPROVEMENTS - REGARDS: FIRST DEVELOPERS -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Volunteer Information Sheet - {{ $volunteer->detail->full_name ?? $volunteer->nickname }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            width: 8.27in;
            height: 11.69in;
            position: relative;
            font-family: Arial, sans-serif;
        }

        .form-container {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .absolute {
            position: absolute;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            font-weight: normal;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 9px;
        }

        .medium {
            font-size: 10px;
        }

        .large {
            font-size: 12px;
        }

        /* Profile Picture */
        #profile_picture {
            top: 70px;
            left: 58px;
            width: auto;
            height: 180px;
        }

        /* Ministry */
        #ministry {
            top: 195px;
            left: 450px;
            width: 370px;
        }

        /* Applied Date and Regular Duration */
        #applied_date {
            top: 295px;
            left: 450px;
            width: 160px;
        }

        #regular_duration {
            top: 295px;
            left: 690px;
            width: 130px;
        }

        /* Basic Info Fields */
        #nickname {
            top: 355px;
            left: 90px;
            width: 240px;
        }

        #full_name_last {
            top: 372px;
            left: 380px;
            width: 130px;
        }

        #full_name_first {
            top: 372px;
            left: 550px;
            width: 130px;
        }

        #full_name_mi {
            top: 372px;
            left: 730px;
            width: 75px;
        }

        #date_of_birth {
            top: 410px;
            left: 120px;
            width: 200px;
        }

        #sex_male,
        #sex_female {
            top: 450px;
            font-size: 16px;
            font-weight: bold;
        }

        #sex_male {
            left: 97px;
        }

        #sex_female {
            left: 172px;
        }

        #address {
            top: 530px;
            left: 50px;
            width: 230px;
        }

        #mobile_number {
            top: 610px;
            left: 125px;
            width: 195px;
        }

        #email_address {
            top: 665px;
            left: 70px;
            width: 210px;
        }

        #occupation {
            top: 710px;
            left: 85px;
            width: 235px;
        }

        /* Civil Status checkboxes */
        #civil_single,
        #civil_widow,
        #civil_separated {
            top: 760px;
            font-size: 16px;
            font-weight: bold;
        }

        #civil_married,
        #civil_church,
        #civil_civil {
            top: 783px;
            font-size: 16px;
            font-weight: bold;
        }

        #civil_others {
            top: 800px;
            font-size: 16px;
            font-weight: bold;
        }

        #civil_single {
            left: 35px;
        }

        #civil_widow {
            left: 114px;
        }

        #civil_separated {
            left: 203px;
        }

        #civil_married {
            left: 35px;
        }

        /* church, civil is not an option in the system. advised to include it for future improvement */
        #civil_church {
            left: 185px;
        }

        #civil_civil {
            left: 240px;
        }

        /* current it is a check mark only. advised to make the input field as text */
        #civil_others {
            left: 90px;
        }

        /* Sacraments */
        /* add marriage for future improvement */
        #sacrament_baptism,
        #sacrament_communion,
        #sacrament_confirmation {
            top: 872px;
            font-size: 12px;
            font-weight: bold;
        }

        #sacrament_baptism {
            left: 26px;
        }

        #sacrament_communion {
            left: 96px;
        }

        #sacrament_confirmation {
            left: 202px;
        }

        /* Formations */
        /* #formation_safeguarding, */
        #formation_bos,
        #formation_diocesan {
            font-size: 12px;
            font-weight: bold;
            left: 50px;
        }

        #formation_bos {
            top: 940px;
        }

        #formation_diocesan {
            top: 962px;
        }

        /* #formation_safeguarding {
            top: 1156px;
        } */

        #formation_others_1,
        #formation_others_2 {
            font-size: 9px;
            left: 80px;
            width: 230px;
        }

        #formation_others_1 {
            top: 1005px;
        }

        #formation_others_2 {
            top: 1030px;
        }

        /* Timeline entries */
        .timeline-row {
            font-size: 9px;
        }

        #timeline_1_org {
            top: 520px;
            left: 370px;
            width: 130px;
        }

        #timeline_1_years {
            top: 520px;
            left: 580px;
            width: 80px;
        }

        #timeline_1_total {
            top: 520px;
            left: 680px;
            width: 30px;
        }

        #timeline_1_active {
            top: 515px;
            left: 740px;
            font-size: 16px;
            font-weight: bold;
        }

        #timeline_2_org {
            top: 557px;
            left: 370px;
            width: 130px;
        }

        #timeline_2_years {
            top: 557px;
            left: 580px;
            width: 80px;
        }

        #timeline_2_total {
            top: 557px;
            left: 680px;
            width: 30px;
        }

        #timeline_2_active {
            top: 555px;
            left: 740px;
            font-size: 16px;
            font-weight: bold;
        }

        #timeline_3_org {
            top: 595px;
            left: 370px;
            width: 130px;
        }

        #timeline_3_years {
            top: 595px;
            left: 580px;
            width: 80px;
        }

        #timeline_3_total {
            top: 595px;
            left: 680px;
            width: 30px;
        }

        #timeline_3_active {
            top: 590px;
            left: 740px;
            font-size: 16px;
            font-weight: bold;
        }

        #timeline_4_org {
            top: 630px;
            left: 370px;
            width: 130px;
        }

        #timeline_4_years {
            top: 630px;
            left: 580px;
            width: 80px;
        }

        #timeline_4_total {
            top: 630px;
            left: 680px;
            width: 30px;
        }

        #timeline_4_active {
            top: 625px;
            left: 740px;
            font-size: 16px;
            font-weight: bold;
        }


        /* Other Affiliations */
        #affiliation_1_org {
            top: 755px;
            left: 370px;
            width: 170px;
        }

        #affiliation_1_years {
            top: 757px;
            left: 600px;
            width: 80px;
        }

        #affiliation_1_active {
            top: 750px;
            left: 750px;
            font-size: 16px;
            font-weight: bold;
        }

        #affiliation_2_org {
            top: 795px;
            left: 370px;
            width: 170px;
        }

        #affiliation_2_years {
            top: 790px;
            left: 600px;
            width: 80px;
        }

        #affiliation_2_active {
            top: 785px;
            left: 750px;
            font-size: 16px;
            font-weight: bold;
        }

        /* Signature and Date */
        #signature_date {
            top: 965px;
            left: 655px;
            width: 90px;
            text-align: center;
        }
    </style>
</head>

<body>
    <img class="form-container" src="{{ url('/assets/img/template.jpg') }}" alt="Volunteer Information Sheet Template">

    <!-- Profile Picture -->
    @if($volunteer->profile_picture)
        <img id="profile_picture" class="absolute" src="{{ asset('storage/' . $volunteer->profile_picture) }}"
            alt="Profile Picture" style="object-fit: cover; border: 1px solid #ccc;">
    @endif

    <!-- Ministry -->
    <p id="ministry" class="absolute">{{ $volunteer->detail->ministry->ministry_name ?? '' }}</p>

    <!-- Applied Date and Regular Duration -->
    <p id="applied_date" class="absolute">{{ $volunteer->detail->applied_month_year ?? '' }}</p>
    <p id="regular_duration" class="absolute">{{ $volunteer->detail->regular_years_month ?? '' }}</p>

    <!-- Basic Info -->
    <p id="nickname" class="absolute">{{ $volunteer->nickname }}</p>

    @php
        function parseFilipinoName($fullName)
        {
            if (empty($fullName)) {
                return ['lastName' => '', 'firstName' => '', 'middleInitial' => ''];
            }

            $parts = explode(' ', trim($fullName));
            $totalParts = count($parts);

            if ($totalParts == 1) {
                // Only one name provided
                return ['lastName' => $parts[0], 'firstName' => '', 'middleInitial' => ''];
            }

            // Last part is always the last name
            $lastName = array_pop($parts);

            // Check if the last remaining part looks like a middle initial (single letter + period, or just single letter)
            $middleInitial = '';
            if (!empty($parts)) {
                $lastPart = end($parts);
                if (preg_match('/^[A-Z]\.?$/', $lastPart)) {
                    $middleInitial = array_pop($parts);
                    // Ensure it has a period
                    if (!str_ends_with($middleInitial, '.')) {
                        $middleInitial .= '.';
                    }
                }
            }

            // Handle common Filipino surname prefixes (De La, Del, San, etc.)
            if (!empty($parts)) {
                $possiblePrefix = strtolower(end($parts));
                if (in_array($possiblePrefix, ['de', 'del', 'dela', 'san', 'santa'])) {
                    $lastName = array_pop($parts) . ' ' . $lastName;
                }
            }

            // Everything remaining is the first name
            $firstName = implode(' ', $parts);

            return [
                'lastName' => $lastName,
                'firstName' => $firstName,
                'middleInitial' => $middleInitial
            ];
        }

        $parsedName = parseFilipinoName($volunteer->detail->full_name ?? '');
        $lastName = $parsedName['lastName'];
        $firstName = $parsedName['firstName'];
        $middleInitial = $parsedName['middleInitial'];
    @endphp

    <p id="full_name_last" class="absolute">{{ $lastName }}</p>
    <p id="full_name_first" class="absolute">{{ $firstName }}</p>
    <p id="full_name_mi" class="absolute">{{ $middleInitial }}</p>

    <p id="date_of_birth" class="absolute">
        {{ $volunteer->date_of_birth ? $volunteer->date_of_birth->format('m / d / Y') : '' }}
    </p>

    <!-- Sex checkboxes -->
    <p id="sex_male" class="absolute">{{ $volunteer->sex == 'Male' ? '✓' : '' }}</p>
    <p id="sex_female" class="absolute">{{ $volunteer->sex == 'Female' ? '✓' : '' }}</p>

    <p id="address" class="absolute">{{ $volunteer->address }}</p>
    <p id="mobile_number" class="absolute">{{ $volunteer->mobile_number }}</p>
    <p id="email_address" class="absolute">{{ $volunteer->email_address }}</p>
    <p id="occupation" class="absolute">{{ $volunteer->occupation }}</p>

    <!-- Civil Status checkboxes -->
    <p id="civil_single" class="absolute">{{ $volunteer->civil_status == 'Single' ? '✓' : '' }}</p>
    <p id="civil_widow" class="absolute">{{ $volunteer->civil_status == 'Widow/er' ? '✓' : '' }}</p>
    <p id="civil_separated" class="absolute">{{ $volunteer->civil_status == 'Separated' ? '✓' : '' }}</p>
    <p id="civil_married" class="absolute">{{ $volunteer->civil_status == 'Married' ? '✓' : '' }}</p>
    <p id="civil_church" class="absolute">{{ $volunteer->civil_status == 'Church' ? '✓' : '' }}</p>
    <p id="civil_civil" class="absolute">{{ $volunteer->civil_status == 'Civil' ? '✓' : '' }}</p>
    <p id="civil_others" class="absolute">{{ $volunteer->civil_status == 'Others' ? '✓' : '' }}</p>

    <!-- Sacraments -->
    @php
        $sacramentNames = $volunteer->sacraments->pluck('sacrament_name')->toArray();
    @endphp
    <p id="sacrament_baptism" class="absolute">{{ in_array('Baptism', $sacramentNames) ? '✓' : '' }}</p>
    <p id="sacrament_communion" class="absolute">{{ in_array('First Communion', $sacramentNames) ? '✓' : '' }}</p>
    <p id="sacrament_confirmation" class="absolute">{{ in_array('Confirmation', $sacramentNames) ? '✓' : '' }}</p>

    <!-- Formations -->
    @php
        $formationNames = $volunteer->formations->pluck('formation_name')->toArray();
        $otherFormations = $volunteer->formations->whereNotIn('formation_name', ['BOS', 'Diocesan Basic Formation', 'Safeguarding Policy'])->take(2);
    @endphp
    <p id="formation_bos" class="absolute">{{ in_array('BOS', $formationNames) ? '✓' : '' }}</p>
    <p id="formation_diocesan" class="absolute">{{ in_array('Diocesan Basic Formation', $formationNames) ? '✓' : '' }}
    </p>
    <p id="formation_safeguarding" class="absolute">{{ in_array('Safeguarding Policy', $formationNames) ? '✓' : '' }}
    </p>

    @foreach($otherFormations as $index => $formation)
        <p id="formation_others_{{ $index + 1 }}" class="absolute">{{ $formation->formation_name }} ({{ $formation->year }})
        </p>
    @endforeach

    <!-- Timeline entries -->
    @foreach($volunteer->timelines->take(7) as $index => $timeline)
        @php $rowNum = $index + 1; @endphp
        <p id="timeline_{{ $rowNum }}_org" class="absolute timeline-row">{{ $timeline->organization_name }}</p>
        <p id="timeline_{{ $rowNum }}_years" class="absolute timeline-row">
            {{ $timeline->year_started }}{{ $timeline->year_ended ? ' - ' . $timeline->year_ended : ' - Present' }}
        </p>
        <p id="timeline_{{ $rowNum }}_total" class="absolute timeline-row center">{{ $timeline->total_years }}</p>
        <p id="timeline_{{ $rowNum }}_active" class="absolute">{{ $timeline->is_active ? '✓' : '' }}</p>
    @endforeach

    <!-- Other Affiliations -->
    @foreach($volunteer->affiliations->take(4) as $index => $affiliation)
        @php $rowNum = $index + 1; @endphp
        <p id="affiliation_{{ $rowNum }}_org" class="absolute timeline-row">{{ $affiliation->organization_name }}</p>
        <p id="affiliation_{{ $rowNum }}_years" class="absolute timeline-row">
            {{ $affiliation->year_started }}{{ $affiliation->year_ended ? ' - ' . $affiliation->year_ended : ' - Present' }}
        </p>
        <p id="affiliation_{{ $rowNum }}_active" class="absolute">{{ $affiliation->is_active ? '✓' : '' }}</p>
    @endforeach

    <!-- Signature Date -->
    <p id="signature_date" class="absolute">{{ now()->format('m/d/Y') }}</p>

    <script>
        window.addEventListener('load', () => {
            // Auto-adjust text size for elements that might overflow
            document.querySelectorAll('p').forEach(p => {
                if (p.scrollWidth > p.offsetWidth) {
                    adjustTextSize(p);
                }
            });

            function adjustTextSize(element) {
                let fontSize = parseFloat(window.getComputedStyle(element).fontSize);
                while (element.scrollWidth > element.offsetWidth && fontSize > 6) {
                    fontSize -= 0.5;
                    element.style.fontSize = `${fontSize}px`;
                }
            }

            // Print the document
            window.print();

            // Close window after printing
            setTimeout(() => {
                window.close();
            }, 500);
        });
    </script>
</body>

</html>