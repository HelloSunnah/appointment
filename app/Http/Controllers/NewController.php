<?php

namespace App\Http\Controllers;

use App\Models\Hr;
use Carbon\Carbon;
use App\Models\Schedule;
use Illuminate\Http\Request;

class NewController extends Controller
{
    public function appointment(){
        {
            $appointments = Schedule::whereDate('schedule_date', Carbon::today())
                                ->get();
            return view('appointment1', ['appointments' => $appointments]);

        }}

        public function getAppointmentsForDate(Request $request)
        {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $request->input('date'));
            
            $appointments = Schedule::whereDate('schedule_date', $selectedDate)->get();
            
            return response()->json($appointments);
        

    }
    public function updateStatus(Request $request, $id)
    {
        $appointment = Schedule::find($id);
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Update appointment status or any other logic as needed
        $appointment->status = 'updated';
        $appointment->save();

        return response()->json(['message' => 'Appointment status updated successfully']);
    }   

    public function hr_create(){
        $hrs=Hr::all();
        return view('pages.HrCreate',compact('hrs'));


    }
    public function hr_create_post(Request $request)  {
       
        Hr::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            
        ]);
        toastr()->addSuccess('The HR has Created');

        return back();
    }

    public function appointment_create(){
        $appointment=Schedule::all();
        $hrs=Hr::all();
        return view('pages.Appointment',compact('hrs','appointment'));
    }


    public function appointment_create_post(Request $request)  {
       

        {
            $hrId = $request->input('hr_id');
            $scheduleDate = $request->input('schedule_date');
        
            $hrSchedulesCount = Schedule::where('hr_id', $hrId)
                ->whereDate('schedule_date', $scheduleDate)
                ->count();
        
            if ($hrSchedulesCount >= 5) {
                toastr()->addError('The HR has already scheduled 5 appointments for the selected date.');

                return redirect()->back();
            }
        
            $existingSchedule = Schedule::where('hr_id', $hrId)
                ->where('schedule_date', $scheduleDate)
                ->where('schedule_start', $request->input('schedule_start'))
                ->where('schedule_end', $request->input('schedule_end'))
                ->count();
        
            if ($existingSchedule > 0) {
                toastr()->addError('The HR has already scheduled an appointment with the same details.');

                return redirect()->back();
            }
        
            Schedule::create([
                'hr_id' => $hrId,
                'candidate_name' => $request->input('candidate_name'),
                'schedule_date' => $scheduleDate,
                'schedule_start' => $request->input('schedule_start'),
                'schedule_end' => $request->input('schedule_end'),
            ]);
            toastr()->addSuccess('Schedule saved successfully.');

            return redirect()->route('appointment.create');
        }
        
}}
