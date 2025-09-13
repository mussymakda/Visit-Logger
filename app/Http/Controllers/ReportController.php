<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function exportDesignerReport()
    {
        $designers = User::where('role', 'interior_designer')
            ->with(['visits.sponsor'])
            ->get();
        
        $filename = 'designer-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($designers) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Designer Name',
                'Designer Email',
                'Sponsor Name',
                'Company',
                'Contact',
                'Location',
                'Visit Count',
                'First Visit',
                'Last Visit',
                'Recent Notes',
            ]);
            
            // Add data rows
            foreach ($designers as $designer) {
                $visits = $designer->visits;
                $uniqueSponsors = $visits->pluck('sponsor')->unique('id');
                
                if ($uniqueSponsors->count() > 0) {
                    foreach ($uniqueSponsors as $sponsor) {
                        $sponsorVisits = $visits->where('sponsor_id', $sponsor->id);
                        fputcsv($file, [
                            $designer->name,
                            $designer->email,
                            $sponsor->name,
                            $sponsor->company_name,
                            $sponsor->contact,
                            $sponsor->location,
                            $sponsorVisits->count(),
                            $sponsorVisits->min('created_at') ? \Carbon\Carbon::parse($sponsorVisits->min('created_at'))->format('Y-m-d H:i:s') : '',
                            $sponsorVisits->max('created_at') ? \Carbon\Carbon::parse($sponsorVisits->max('created_at'))->format('Y-m-d H:i:s') : '',
                            $sponsorVisits->sortByDesc('created_at')->first()?->notes ?? 'No notes',
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        $designer->name,
                        $designer->email,
                        'No sponsors visited',
                        'N/A',
                        'N/A',
                        'N/A',
                        0,
                        '',
                        '',
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportSponsorReport()
    {
        $sponsors = Sponsor::with(['visits.user'])->get();
        
        $filename = 'sponsor-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($sponsors) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Sponsor Name',
                'Company',
                'Has Logo',
                'Google Reviews Link',
                'Contact',
                'Location',
                'Designer Name',
                'Designer Email',
                'Visit Count',
                'First Visit',
                'Last Visit',
            ]);
            
            // Add data rows
            foreach ($sponsors as $sponsor) {
                $visits = $sponsor->visits;
                $uniqueDesigners = $visits->pluck('user')->unique('id');
                
                if ($uniqueDesigners->count() > 0) {
                    foreach ($uniqueDesigners as $designer) {
                        $designerVisits = $visits->where('user_id', $designer->id);
                        fputcsv($file, [
                            $sponsor->name,
                            $sponsor->company_name,
                            $sponsor->logo ? 'Yes' : 'No',
                            $sponsor->google_reviews_link,
                            $sponsor->contact,
                            $sponsor->location,
                            $designer->name,
                            $designer->email,
                            $designerVisits->count(),
                            $designerVisits->min('created_at') ? \Carbon\Carbon::parse($designerVisits->min('created_at'))->format('Y-m-d H:i:s') : '',
                            $designerVisits->max('created_at') ? \Carbon\Carbon::parse($designerVisits->max('created_at'))->format('Y-m-d H:i:s') : '',
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        $sponsor->name,
                        $sponsor->company_name,
                        $sponsor->logo ? 'Yes' : 'No',
                        $sponsor->google_reviews_link,
                        $sponsor->contact,
                        $sponsor->location,
                        'No visitors',
                        'N/A',
                        0,
                        '',
                        '',
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportAllVisitsReport()
    {
        $visits = \App\Models\Visit::with(['user', 'sponsor'])->orderBy('created_at', 'desc')->get();
        
        $filename = 'all-visits-report-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($visits) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Visit Date',
                'Designer Name',
                'Designer Email',
                'Sponsor Name',
                'Company Name',
                'Contact',
                'Location',
                'Notes',
            ]);
            
            // Add data rows
            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->created_at->format('Y-m-d H:i:s'),
                    $visit->user->name,
                    $visit->user->email,
                    $visit->sponsor->name,
                    $visit->sponsor->company_name,
                    $visit->sponsor->contact,
                    $visit->sponsor->location,
                    $visit->notes ?? 'No notes',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
