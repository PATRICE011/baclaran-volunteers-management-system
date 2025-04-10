<!-- resources/views/volunteer-form.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Information Sheet</title>

    <!-- WEBSITE FAVICON -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/baclaran-church-logo.jpg')}}">

    <!-- REMIX ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">

    <!-- WEBISTE FAVICON -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/information_sheet.css') }}">
</head>

<body>
    <div class="form-container">
        <div class="row">
            <div class="col-md-4">
                <div class="photo-box"></div>

                <div class="section-header text-center">BASIC INFO</div>

                <div class="mb-3">
                    <label class="info">Nickname</label>
                    <input type="text" class="form-control form-field">
                </div>

                <div class="mb-3">
                    <label class="info">Date of Birth <small>MM / DD / YY</small></label>
                    <input type="date" class="form-control form-field">
                </div>

                <div class="mb-3">
                    <div>
                        
                    </div>
                    <label class="info">Sex:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="male" value="male">
                        <label class="form-check-label" for="male">Male</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="female" value="female">
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="info">
                        <i class="ri-map-pin-fill basic_info-icon"></i> Address:
                    </label>
                    <input type="text" class="form-control form-field">
                </div>

                <div class="mb-3">
                    <label class="info">
                        <i class="ri-phone-fill basic_info-icon"></i> Mobile Number:</label>
                    <input type="tel" class="form-control form-field">
                </div>

                <div class="mb-3">
                    <label class="info">
                        <i class="ri-mail-fill basic_info-icon"></i> Email Address:</label>
                    <input type="email" class="form-control form-field">
                </div>

                <div class="mb-3">
                    <label class="info">Occupation:</label>
                    <input type="text" class="form-control form-field">
                </div>

                <div class="mb-3">
                    <label class="info">Civil Status:</label>
                    <div class="row ms-2">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="civilStatus" id="single" value="single">
                                <label class="form-check-label" for="single">Single</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="civilStatus" id="widower" value="widower">
                                <label class="form-check-label" for="widower">Widow/er</label>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="civilStatus" id="separated" value="separated">
                                <label class="form-check-label" for="separated">Separated</label>
                            </div>
                        </div>
                    </div>

                    <div class="row ms-2 mt-2">
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="civilStatus" id="married" value="married">
                                <label class="form-check-label" for="married">Married</label>
                            </div>
                        </div>

                        <div class="col-4">
                            <p>
                                <strong>If Married</strong>
                            </p>
                        </div>

                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="marriageType" id="church" value="church">
                                <label class="form-check-label" for="church">Church</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="marriageType" id="civil" value="civil">
                                <label class="form-check-label" for="civil">Civil</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="info">Others:</label>
                        <input type="text" class="form-control form-field">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="info">Sacraments Received:</label>
                    <div class="row ms-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="baptism">
                            <label class="form-check-label" for="baptism">Baptism</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="communion">
                            <label class="form-check-label" for="communion">First Communion</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmation">
                            <label class="form-check-label" for="confirmation">Confirmation</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="info">Formations Received:</label>
                    <div class="ms-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bos">
                            <label class="form-check-label" for="bos">Basic Orientation Seminar (BOS)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bff">
                            <label class="form-check-label" for="bff">Basic Faith Formation (BFF)</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>OTHERS:</label>
                    <input type="text" class="form-control form-field">
                </div>
            </div>

            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="header-title-1">National Shrine of</div>
                        <h4 class="header-title-2">OUR MOTHER OF <br> PERPETUAL HELP</h4>
                        <div class="header-subtitle">REDEMPTORIST ROAD, BACLARAN, PARAÑAQUE CITY</div>
                    </div>
                    <img src="{{ asset('assets/img/baclaran-church-logo.jpg') }}" alt="Baclaran Church Logo" class="logo">
                </div>

                <div class="section-header-2">
                    VOLUNTEER'S INFORMATION SHEET
                </div>

                <div class="d-flex align-items-center mb-2">
                    <label class="me-2 mb-0" style="min-width: 100px;">MINISTRY:</label>
                    <input type="text" class="form-control form-field">
                </div>

                <small class="text-muted">If member of BMM, PYM and DCComm, please specify the group.</small>
                <input type="text" class="form-control form-field mt-2">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Month & Year applied as Volunteer:</label>
                        <input type="text" class="form-control form-field">
                    </div>
                    <div class="col-md-6">
                        <label>No. of Years/Month as Regular Volunteer:</label>
                        <input type="text" class="form-control form-field">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Name: <small>Surname, First Name, M.I.</small></label>
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-field">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-field">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control form-field">
                        </div>
                    </div>
                </div>

                <div class="section-header">
                    <i class="ri-account-circle-line user-icon"></i>
                    VOLUNTEER TIMELINE
                </div>
                <p>Please indicate (if any) all Organization/Ministry you belong to in the Shrine</p>

                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name of Organization/Ministry</th>
                            <th>Year Started-Year Ended</th>
                            <th>Total Years</th>
                            <th>Active? Y/N</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="section-header">
                    <i class="ri-account-circle-line user-icon"></i>
                    OTHER AFFILIATIONS
                </div>
                <p>Please indicate (if any) all Organization/Ministry you belong to outside the Shrine</p>

                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name of Organization/Ministry</th>
                            <th>Year Started-Year Ended</th>
                            <th>Active? Y/N</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                            <td><input type="text" class="form-control form-field border-0"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-4">
                    <p><strong>Privacy Notice:</strong> The personal information collected in this form are part of the requirement for your application as volunteer of the National Shrine of Mother of Perpetual Help. The processing, and disposal of any personal data shall be in accordance with the provisions of the <strong>Republic Act 10173</strong> or the <strong>Data Privacy Act of 2012 (DPA)</strong>. For more information on the <strong>Shrine's Data Privacy Policy</strong>, please visit this site: https://baclaranchurch.org/privacy.html</p>

                    <p>I hereby affix my Signature to certify that all the above information is true and to allow processing of my personal data in accordance with the <strong>Data Privacy Act</strong>.</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>VOLUNTEER'S SIGNATURE</label>
                            <input type="text" class="form-control form-field">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Date</label>
                            <input type="text" class="form-control form-field">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Noted by:</label>
                            <input type="text" class="form-control form-field">
                            <small class="text-center d-block">NAME & SIGNATURE OF MINISTRY DIRECTOR</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Approved by:</label>
                            <input type="text" class="form-control form-field">
                            <small class="text-center d-block">NAME & SIGNATURE OF PRESENT COORDINATOR</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>