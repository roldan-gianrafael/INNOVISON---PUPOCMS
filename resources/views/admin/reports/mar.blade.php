@extends('layouts.admin')

@section('title', 'MAR Report')

@push('styles')
<style>
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    h2 { color: #8B0000; font-size: 22px; margin-bottom: 20px; border-bottom: 2px solid #8B0000; padding-bottom: 10px; }
    h3 { color: #334155; font-size: 18px; margin-top: 30px; }

    /* Stats Widget */
    .stats-container { display: flex; gap: 20px; margin-bottom: 25px; }
    .stat-card { background: #8B0000; color: white; padding: 15px 25px; border-radius: 10px; flex: 1; }
    .stat-card span { display: block; font-size: 12px; opacity: 0.8; text-transform: uppercase; }
    .stat-card strong { font-size: 24px; }

    /* Table Styling */
    .mar-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .mar-table th { background: #f8fafc; color: #64748b; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
    .mar-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    .category-row { background: #fff5f5; font-weight: 700; color: #8B0000; }
    .section-row td { background: #70131B; color: #ffffff; font-weight: 800; }
    .subsection-row td { background: #fef2f2; color: #7f1d2d; font-weight: 800; }
    .nested-row td:first-child { padding-left: 30px; }
    .deep-nested-row td:first-child { padding-left: 48px; }
    .gad-table { width: 100%; border-collapse: collapse; margin-top: 26px; }
    .gad-table th { background: #f8fafc; color: #64748b; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
    .gad-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    .gad-section-row td { background: #8f2230; color: #ffffff; font-weight: 800; }
    
    /* CRUD Form */
    .manage-section { background: #fdfdfd; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 10px; margin-top: 40px; }
    .form-group { margin-bottom: 15px; }
    .btn-save { background: #70131B; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
    .btn-delete { color: #ef4444; text-decoration: none; font-size: 12px; margin-left: 10px; }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $isAdminLike = $role === \App\Models\User::ROLE_SUPERADMIN;
    $reportsHomeUrl = $role === \App\Models\User::ROLE_ADMIN ? url('/assistant/reports') : url('/admin/reports');
@endphp

@php
    $consultationGad = $gadTables['consultation'] ?? [];
    $certificateGad = $gadTables['certificate'] ?? [];
    $triageOnlineGad = $gadTables['triage_online'] ?? [];
    $combinedGad = $gadTables['combined'] ?? [];
@endphp



 <a href="{{ $reportsHomeUrl }}" style="color: #64748b; text-decoration: none;">&larr; Back to Reports</a>



<div class="stats-container">
    <div class="stat-card">
        <span>Total Consultations Today</span>
        <strong>{{ $totalToday ?? 0 }}</strong>
    </div>
    <div class="stat-card" style="background: #8f2230;">
        <span>Current Filter Month</span>
        <strong>{{ date('F Y', strtotime($month)) }}</strong>
    </div>
</div>

@if($isAdminLike)
    <a href="{{ route('admin.reports.manage-mar', ['month' => $month]) }}" 
       class="btn-filter" 
       style="background:#70131B; color:white; padding:8px 15px; border-radius:6px; text-decoration:none; font-size:14px;">
       Manage MAR
    </a>
@endif
<div class="card">
    <h2>Medical Accomplishment Report</h2>

    <form method="GET" class="filter-box" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="month" name="month" class="form-control" value="{{ $month }}">
        <button class="btn-filter" type="submit" style="background:#8B0000; color:white; padding:8px 15px; border-radius:6px; border:none;">Generate</button>
    </form>
   

    

    @php
        $resolveConsultationPatientType = function ($consultation) {
            $value = strtolower(trim((string) ($consultation->user_role ?: $consultation->user_type ?: '')));

            return match ($value) {
                'student' => 'student',
                'faculty' => 'faculty',
                'admin', 'staff' => 'admin',
                'dependent', 'dependents' => 'dependent',
                default => null,
            };
        };

        $countByPatientType = function ($consultations, string $type) use ($resolveConsultationPatientType) {
            return $consultations->filter(function ($consultation) use ($resolveConsultationPatientType, $type) {
                return $resolveConsultationPatientType($consultation) === $type;
            })->count();
        };

        $countCertificateByType = function ($consultations, string $certificateType, string $patientType) use ($countByPatientType) {
            $filtered = $consultations->filter(function ($consultation) use ($certificateType) {
                return trim((string) ($consultation->certificate_type ?? 'none')) === $certificateType;
            });

            return $countByPatientType($filtered, $patientType);
        };

        $consultationTotals = ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0];
        foreach ($categories as $cat) {
            foreach ($cat->medicalConditions as $condition) {
                $consultationTotals['student'] += $countByPatientType($condition->consultations, 'student');
                $consultationTotals['faculty'] += $countByPatientType($condition->consultations, 'faculty');
                $consultationTotals['admin'] += $countByPatientType($condition->consultations, 'admin');
                $consultationTotals['dependent'] += $countByPatientType($condition->consultations, 'dependent');
            }
        }
        $consultationGrandTotal = array_sum($consultationTotals);

        $excusedLetterTotals = ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0];
        $cocIjtTotals = ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0];
        $cocLadderizedTotals = ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0];
        $excusedLetterCategoryRows = collect();
        $onlineTotals = [
            'consultation' => ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0],
            'medical_clearance' => ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0],
            'others' => ['student' => 0, 'faculty' => 0, 'admin' => 0, 'dependent' => 0],
        ];

        foreach ($categories as $cat) {
            $categoryConsultations = $cat->medicalConditions->flatMap->consultations;

            $categoryExcused = [
                'student' => $countCertificateByType($categoryConsultations, 'excused_letter', 'student'),
                'faculty' => $countCertificateByType($categoryConsultations, 'excused_letter', 'faculty'),
                'admin' => $countCertificateByType($categoryConsultations, 'excused_letter', 'admin'),
                'dependent' => $countCertificateByType($categoryConsultations, 'excused_letter', 'dependent'),
            ];

            if (array_sum($categoryExcused) > 0) {
                $excusedLetterCategoryRows->push([
                    'label' => 'Category ' . $cat->code . ' - ' . $cat->name,
                    'student' => $categoryExcused['student'],
                    'faculty' => $categoryExcused['faculty'],
                    'admin' => $categoryExcused['admin'],
                    'dependent' => $categoryExcused['dependent'],
                    'total' => array_sum($categoryExcused),
                ]);
            }

            foreach (['student', 'faculty', 'admin', 'dependent'] as $type) {
                $excusedLetterTotals[$type] += $categoryExcused[$type];
                $cocIjtTotals[$type] += $countCertificateByType($categoryConsultations, 'coc_ijt', $type);
                $cocLadderizedTotals[$type] += $countCertificateByType($categoryConsultations, 'coc_ladderized', $type);
            }

            $onlineConsultations = $categoryConsultations->filter(function ($consultation) {
                return trim((string) ($consultation->consultation_source ?? '')) === 'online';
            });

            $onlineBuckets = [
                'consultation' => $onlineConsultations->filter(function ($consultation) {
                    $service = strtolower(trim((string) ($consultation->service ?? '')));
                    return in_array($service, ['general consultation', 'consultation'], true);
                }),
                'medical_clearance' => $onlineConsultations->filter(function ($consultation) {
                    $service = strtolower(trim((string) ($consultation->service ?? '')));
                    return str_contains($service, 'clearance');
                }),
            ];
            $onlineBuckets['others'] = $onlineConsultations->reject(function ($consultation) {
                $service = strtolower(trim((string) ($consultation->service ?? '')));
                return in_array($service, ['general consultation', 'consultation'], true) || str_contains($service, 'clearance');
            });

            foreach ($onlineBuckets as $bucket => $consultations) {
                foreach (['student', 'faculty', 'admin', 'dependent'] as $type) {
                    $onlineTotals[$bucket][$type] += $countByPatientType($consultations, $type);
                }
            }
        }
    @endphp

    <table class="mar-table">
        <thead>
            <tr>
                <th>Service Category / Conditions</th>
                <th style="text-align: center;">Student</th>
                <th style="text-align: center;">Faculty</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Dependent</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-row">
                <td colspan="6">i. Consultation / Treatment</td>
            </tr>
            @foreach($categories as $cat)
    <tr style="background-color: #f8f9fa; font-weight: bold;">
        <td colspan="6">Category {{ $cat->code }} - {{ $cat->name }}</td>
    </tr>

        @foreach($cat->medicalConditions as $condition)
        @php
            $stu = $countByPatientType($condition->consultations, 'student');
            $fac = $countByPatientType($condition->consultations, 'faculty');
            $adm = $countByPatientType($condition->consultations, 'admin');
            $dep = $countByPatientType($condition->consultations, 'dependent');
            $total = $stu + $fac + $adm + $dep;
        @endphp
        <tr>
            <td style="padding-left: 30px;">{{ $condition->name }}</td>
            <td class="text-center">{{ $stu }}</td>
            <td class="text-center">{{ $fac }}</td>
            <td class="text-center">{{ $adm }}</td>
            <td class="text-center">{{ $dep }}</td>
            <td class="text-center">{{ $total }}</td>
        </tr>
    @endforeach
@endforeach
            <tr class="subsection-row">
                <td>Total Consultation</td>
                <td class="text-center">{{ $consultationTotals['student'] }}</td>
                <td class="text-center">{{ $consultationTotals['faculty'] }}</td>
                <td class="text-center">{{ $consultationTotals['admin'] }}</td>
                <td class="text-center">{{ $consultationTotals['dependent'] }}</td>
                <td class="text-center">{{ $consultationGrandTotal }}</td>
            </tr>

            <tr class="section-row"><td colspan="6">ii. Medical Certificate / Clearance - Certificate of Compliance</td></tr>
            <tr class="subsection-row nested-row">
                <td>A. Excused Letter</td>
                <td class="text-center">{{ $excusedLetterTotals['student'] }}</td>
                <td class="text-center">{{ $excusedLetterTotals['faculty'] }}</td>
                <td class="text-center">{{ $excusedLetterTotals['admin'] }}</td>
                <td class="text-center">{{ $excusedLetterTotals['dependent'] }}</td>
                <td class="text-center">{{ array_sum($excusedLetterTotals) }}</td>
            </tr>
            @forelse($excusedLetterCategoryRows as $categoryRow)
                <tr class="nested-row deep-nested-row">
                    <td>{{ $categoryRow['label'] }}</td>
                    <td class="text-center">{{ $categoryRow['student'] }}</td>
                    <td class="text-center">{{ $categoryRow['faculty'] }}</td>
                    <td class="text-center">{{ $categoryRow['admin'] }}</td>
                    <td class="text-center">{{ $categoryRow['dependent'] }}</td>
                    <td class="text-center">{{ $categoryRow['total'] }}</td>
                </tr>
            @empty
                <tr class="nested-row deep-nested-row"><td>No excused letter category recorded yet.</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>
            @endforelse
            <tr class="subsection-row nested-row">
                <td>B. COC for IJT</td>
                <td class="text-center">{{ $cocIjtTotals['student'] }}</td>
                <td class="text-center">{{ $cocIjtTotals['faculty'] }}</td>
                <td class="text-center">{{ $cocIjtTotals['admin'] }}</td>
                <td class="text-center">{{ $cocIjtTotals['dependent'] }}</td>
                <td class="text-center">{{ array_sum($cocIjtTotals) }}</td>
            </tr>
            <tr class="subsection-row nested-row">
                <td>C. COC for Ladderized</td>
                <td class="text-center">{{ $cocLadderizedTotals['student'] }}</td>
                <td class="text-center">{{ $cocLadderizedTotals['faculty'] }}</td>
                <td class="text-center">{{ $cocLadderizedTotals['admin'] }}</td>
                <td class="text-center">{{ $cocLadderizedTotals['dependent'] }}</td>
                <td class="text-center">{{ array_sum($cocLadderizedTotals) }}</td>
            </tr>

            <tr class="section-row"><td colspan="6">iii. Injections</td></tr>
            <tr><td class="nested-row">Injection Services</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>

            <tr class="section-row"><td colspan="6">iv. Referrals</td></tr>
            <tr class="subsection-row nested-row"><td>A. Ref. to Hospital without nurse</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>
            <tr class="subsection-row nested-row"><td>B. Ref. to Hospital with nurse</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>
            <tr class="subsection-row nested-row"><td>C. Referral</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>

            <tr class="section-row"><td colspan="6">v. Others</td></tr>
            <tr><td class="nested-row">Other Services</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>

            <tr class="section-row"><td colspan="6">vi. On-line Consultation</td></tr>
            <tr class="subsection-row nested-row"><td>A. Consultation</td><td class="text-center">{{ $onlineTotals['consultation']['student'] }}</td><td class="text-center">{{ $onlineTotals['consultation']['faculty'] }}</td><td class="text-center">{{ $onlineTotals['consultation']['admin'] }}</td><td class="text-center">{{ $onlineTotals['consultation']['dependent'] }}</td><td class="text-center">{{ array_sum($onlineTotals['consultation']) }}</td></tr>
            <tr class="subsection-row nested-row"><td>B. Medical Clearance</td><td class="text-center">{{ $onlineTotals['medical_clearance']['student'] }}</td><td class="text-center">{{ $onlineTotals['medical_clearance']['faculty'] }}</td><td class="text-center">{{ $onlineTotals['medical_clearance']['admin'] }}</td><td class="text-center">{{ $onlineTotals['medical_clearance']['dependent'] }}</td><td class="text-center">{{ array_sum($onlineTotals['medical_clearance']) }}</td></tr>
            <tr class="subsection-row nested-row"><td>C. Others</td><td class="text-center">{{ $onlineTotals['others']['student'] }}</td><td class="text-center">{{ $onlineTotals['others']['faculty'] }}</td><td class="text-center">{{ $onlineTotals['others']['admin'] }}</td><td class="text-center">{{ $onlineTotals['others']['dependent'] }}</td><td class="text-center">{{ array_sum($onlineTotals['others']) }}</td></tr>

            <tr class="section-row"><td colspan="6">vii. Triage Survey</td></tr>
            <tr class="subsection-row nested-row"><td>A. Online</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>

            <tr class="section-row"><td colspan="6">viii. Bulletin Updates</td></tr>
            <tr><td class="nested-row">Bulletin Updates</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td></tr>
        </tbody>
    </table>

    <table class="gad-table">
        <thead>
            <tr>
                <th>GAD (Consultation)</th>
                <th style="text-align: center;">Students</th>
                <th style="text-align: center;">Faculty</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Dependent</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="gad-section-row"><td colspan="6">GAD Summary</td></tr>
            <tr><td>Female</td><td class="text-center">{{ $consultationGad['female']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['female']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['female']['total'] ?? 0 }}</td></tr>
            <tr><td>Male</td><td class="text-center">{{ $consultationGad['male']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['male']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['male']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">PWD</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $consultationGad['pwd_male']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $consultationGad['pwd_female']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['pwd_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">Senior</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $consultationGad['senior_male']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $consultationGad['senior_female']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['senior_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td>Total</td><td class="text-center">{{ $consultationGad['total']['student'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['total']['faculty'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['total']['admin'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['total']['dependent'] ?? 0 }}</td><td class="text-center">{{ $consultationGad['total']['total'] ?? 0 }}</td></tr>
        </tbody>
    </table>

    <table class="gad-table">
        <thead>
            <tr>
                <th>GAD (OJT/Medical Compliance/Medical for Promotion/Excused Letter)</th>
                <th style="text-align: center;">Students</th>
                <th style="text-align: center;">Faculty</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Dependent</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="gad-section-row"><td colspan="6">GAD Summary</td></tr>
            <tr><td>Female</td><td class="text-center">{{ $certificateGad['female']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['female']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['female']['total'] ?? 0 }}</td></tr>
            <tr><td>Male</td><td class="text-center">{{ $certificateGad['male']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['male']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['male']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">PWD</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $certificateGad['pwd_male']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $certificateGad['pwd_female']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['pwd_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">Senior</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $certificateGad['senior_male']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $certificateGad['senior_female']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['senior_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td>Total</td><td class="text-center">{{ $certificateGad['total']['student'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['total']['faculty'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['total']['admin'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['total']['dependent'] ?? 0 }}</td><td class="text-center">{{ $certificateGad['total']['total'] ?? 0 }}</td></tr>
        </tbody>
    </table>

    <table class="gad-table">
        <thead>
            <tr>
                <th>GAD (Triage Online)</th>
                <th style="text-align: center;">Students</th>
                <th style="text-align: center;">Faculty</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Dependent</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="gad-section-row"><td colspan="6">GAD Summary</td></tr>
            <tr><td>Female</td><td class="text-center">{{ $triageOnlineGad['female']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['female']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['female']['total'] ?? 0 }}</td></tr>
            <tr><td>Male</td><td class="text-center">{{ $triageOnlineGad['male']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['male']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['male']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">PWD</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $triageOnlineGad['pwd_male']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $triageOnlineGad['pwd_female']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['pwd_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">Senior</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $triageOnlineGad['senior_male']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $triageOnlineGad['senior_female']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['senior_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td>Total</td><td class="text-center">{{ $triageOnlineGad['total']['student'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['total']['faculty'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['total']['admin'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['total']['dependent'] ?? 0 }}</td><td class="text-center">{{ $triageOnlineGad['total']['total'] ?? 0 }}</td></tr>
        </tbody>
    </table>

    <table class="gad-table">
        <thead>
            <tr>
                <th>GAD (Consultation + OJT/Medical Compliance/Medical for Promotion + Triage)</th>
                <th style="text-align: center;">Students</th>
                <th style="text-align: center;">Faculty</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Dependent</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="gad-section-row"><td colspan="6">GAD Summary</td></tr>
            <tr><td>Female</td><td class="text-center">{{ $combinedGad['female']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['female']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['female']['total'] ?? 0 }}</td></tr>
            <tr><td>Male</td><td class="text-center">{{ $combinedGad['male']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['male']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['male']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">PWD</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $combinedGad['pwd_male']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $combinedGad['pwd_female']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['pwd_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td colspan="6">Senior</td></tr>
            <tr><td style="padding-left: 30px;">Male</td><td class="text-center">{{ $combinedGad['senior_male']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_male']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_male']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_male']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_male']['total'] ?? 0 }}</td></tr>
            <tr><td style="padding-left: 30px;">Female</td><td class="text-center">{{ $combinedGad['senior_female']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_female']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_female']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_female']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['senior_female']['total'] ?? 0 }}</td></tr>
            <tr class="subsection-row"><td>Total</td><td class="text-center">{{ $combinedGad['total']['student'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['total']['faculty'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['total']['admin'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['total']['dependent'] ?? 0 }}</td><td class="text-center">{{ $combinedGad['total']['total'] ?? 0 }}</td></tr>
        </tbody>
    </table>
</div>



@endsection
