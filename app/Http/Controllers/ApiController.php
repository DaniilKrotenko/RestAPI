<?php

namespace App\Http\Controllers;

use App\UserShift;
use Illuminate\Http\Request;

use App\Projects;

use DB;
use App\Mail\Email;
use App\Models\User;
use App\Shift;
use App\Events\Message;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use \Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{

//Poject
    public function projectID(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists()) {
            return Projects::find($request->input('project_id'));
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function projectEdit(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists()) {

            $id = $request['project_id'];
            $project = Projects::find($id);
            $project->name = $request->input('name');
            $project->address = $request->input('address');
            $project->projectNumber = $request->input('projectNumber');
            $project->geoFence = $request->input('geoFence');
            $project->radius = $request->input('radius');

            $project->save();

            return response()->json([
                'Request' => 'Project successfully updated',
            ]);
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }

    }


    public function allProjects(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists()) {
            return Projects::all();
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function addProject(Request $request)
    {
        $rules = [
            'name' => 'required|min:2',
            'address' => 'required',
            'projectNumber' => 'required|unique:project,projectNumber'
        ];

        $messages = [
            'name.required' => 'Please write your name',
            'name.min' => 'Name must be more than 2 characters',
            'address.required' => 'Please write your address',
            'projectNumber.required' => 'Please write your project number',
            'projectNumber.unique' => 'A project with this number already exists'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();


        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            if ($validator->fails()) {
                return response()->json([
                    'Error' => $errors->first()
                ]);
            } else {
                $project = Projects::create([
                    'name' => $request->input('name'),
                    'address' => $request->input('address'),
                    'projectNumber' => $request->input('projectNumber'),
                    'geoFence' => $request->input('geoFance'),
                    'radius' => $request->input('radius')
                ]);

                return response()->json([
                    'Request' => 'Project successfully created'
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function projectDelete(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            $idProject = $request['project_id'];
            if (Projects::where('id', $idProject)->exists()) {
                Projects::find($idProject)->delete();
                if (Shift::where('project_id', $idProject)->exists()) {
                    Shift::where('project_id', $idProject)->delete();
                }
                return response()->json([
                    'Request' => 'Project deleted successfully',
                ]);
            } else {
                return response()->json([
                    'Error' => 'There is no such project.',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    // Managers

    public function listManagers(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            return User::all()->where('role_id', '=', '2');
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    //Workers

    public function listWorkers(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            return User::all()->where('role_id', '=', '3');
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }


    public function worker(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            return User::where('user_token', $request['token'])->get()->first();
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function workerEdit(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            $user_token = $request['token'];
            $worker = User::where('user_token', $user_token)->get()->first();
            $worker->active = $request->input('active');
            $worker->first_name = $request->input('first_name');
            $worker->last_name = $request->input('last_name');
            $worker->email = $request->input('email');
            $worker->phone = $request->input('phone');

            $worker->save();

            return response()->json([
                'Request' => 'Worker successfully updated'
            ]);

        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function newWorker(Request $request)
    {

        $rules = [
            'email' => 'required|unique:users|email',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'password' => 'required|min:8'
        ];

        $messages = [
            'email.required' => 'Please write your email',
            'email.email' => 'Please write your email',
            'first_name.required' => 'Please write your first name',
            'last_name.required' => 'Please write your last name',
            'password.required' => 'Please write your password',
            'first_name.min' => 'First name must be more than 2 characters',
            'last_name.min' => 'Last name must be more than 2 characters',
            'password.min' => 'Password must be more than 8 characters'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();

        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $errors->first()
                ]);
            } else {
                User::create([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'role_id' => '3',
                    'user_token' => uniqid(60),
                ]);
                return response()->json([
                    'Request' => 'Worker added',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function workerDelete(Request $request)
    {
        $user_token = $request['token'];
        $worker = User::where('user_token', $user_token)->get()->first();
        if ($worker && User::where('role_id' == 3)) {
            $worker->delete();
            return response()->json([
                'Request' => 'Worker deleted successfully',
            ]);
        } else {
            return response()->json([
                'Error' => 'There is no such worker',
            ]);
        }
    }

    // Users

    public function infoUser(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            if (User::where('id', $request['user_id'])->exists()) {
                return User::where('id', $request['user_id'])->get()->first();
            } else {
                return User::where('user_token', '=', $request['token'])->get()->first();
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }


    public function deactive(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            $user_id = $request['user_id'];
            if (User::where('id', $user_id)) {
                User::where('id', $user_id)->update(['deactive' => 1]);

                return response()->json([
                    'Error' => 'User has been successfully deactivated',
                ]);
            } else {
                return response()->json([
                    'Error' => 'This user does not exist',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function activeUser(Request $request)
    {
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            $user_id = $request['user_id'];
            if (User::where('id', $user_id)) {
                User::where('id', $user_id)->update(['deactive' => 0]);

                return response()->json([
                    'Error' => 'User has been successfully activated',
                ]);
            } else {
                return response()->json([
                    'Error' => 'This user does not exist',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    //Registration

    public function register(Request $request)
    {
        $rules = [
            'email' => 'required|unique:users|email',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'password' => 'required|min:8',
            'phone' => 'required|min:6'
        ];

        $messages = [
            'email.required' => 'Please write your email',
            'email.email' => 'Please write your email',
            'first_name.required' => 'Please write your name',
            'last_name.required' => 'Please write your name',
            'password.required' => 'Please write your password',
            'first_name.min' => 'Name must be more than 2 characters',
            'last_name.min' => 'Name must be more than 2 characters',
            'password.min' => 'Password must be more than 8 characters',
            'phone.required' => 'Please write your phone number',
            'phone.min' => 'Phone number must be more than 6 numbers'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();

        if ($validator->fails()) {
            return response()->json([
                'errors' => $errors->first()
            ]);
        } else {
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone'),
                'role_id' => '2',
                'user_token' => uniqid(60),
            ]);
            return response()->json([
                'Your token: ' => $user->user_token
            ]);
        }

    }


    //Login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|max:100|email|exists:users,email',
            'password' => 'required|exists:users,password',
        ];
        $messages = [
            'email.required' => 'Please write your email',
            'email.email' => 'Please write your email',
            'email.exists' => 'This email does not exist',
            'password.required' => 'Enter your password',
            'password.exists' => 'You entered an incorrect password'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = $validator->errors();


        if (\Auth::attempt($credentials)) {
            if (\Auth::user()->deactive == 0) {
                \Auth::user()->update(['active' => 1]);
                return response()->json([
                    'Your token' => \Auth::user()->generateToken()
                ]);
            } else {
                \Auth::logout();
                return response()->json([
                    'errors' => 'Your account has been blocked. To get an answer about this error, contact your manager'
                ]);
            }
        } else {
            return response()->json([
                'errors' => $errors->first()
            ]);
        }
    }


    //Shifts

    public function shifts(Request $request)
    {

        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            return Shift::all();
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }


    public function addShift(Request $request)
    {

        $rules = [
            'project_id' => 'required|max:100',
            'timeStart' => 'required',
            'timeEnd' => 'required',
            'date' => 'required|date',
            'user_id' => 'required',

        ];
        $messages = [
            'project_id.required' => 'You have not entered the project id',
            'project_id.exists' => 'This project id does not exist',
            'project_id.numeric' => 'Project number, must have only numbers',
            'timeStart.required' => 'Enter the start time',
            'timeEnd.required' => 'Enter the end time',
            'date.required' => 'Enter the date',
            'date.date' => 'Enter the date',
            'user_id.required' => 'Enter user ID or name worker'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = $validator->errors();

        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $errors->first()
                ]);
            } else {
                $shift = Shift::create([
                    'project_id' => $request->input('project_id'),
                    'timeStart' => $request->input('timeStart'),
                    'timeEnd' => $request->input('timeEnd'),
                    'date' => $request->input('date')
                ]);

                foreach ($request->input('user_id') as $id)
                    UserShift::create([
                        'user_id' => $id,
                        'shift_id' => $shift->id,
                    ]);

                return response()->json([
                    "Request" => 'Shift successfully added'
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function oneShifts(Request $request)
    {
        $id = $request['shift_id'];
        $out = Shift::find($id);
        $userShift = UserShift::where('shift_id', $id)->get();
        $workers = [];
        foreach ($userShift as $us) {
            $user = User::find($us->user_id);
            $workers[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->first_name . ' ' . $user->last_name,
            ];
        }
        $out->workers = $workers;

        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            return $out;
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function addWorkerShift(Request $request)
    {
        $id = $request['shift_id'];
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            if (UserShift::where('shift_id', $id)->exists()) {
                UserShift::create([
                    'shift_id' => $id,
                    'user_id' => $request['user_id']
                ]);
                return response()->json([
                    'Request' => 'Worker successfully added ',
                ]);
            } else {
                return response()->json([
                    'Error' => 'No such shift found',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function oneShiftsEdit(Request $request)
    {
        $id = $request['shift_id'];
        $shift = Shift::find($id);
        $userShift = UserShift::where('shift_id', $id)->get();
        $newList = $request['user_id'];
        $newList = array_flip($newList);

        if (User::where('user_token', '=', $request['token'])->exists()) {
            foreach ($userShift as $us) {
                if (!isset($newList[$us->user_id])) {
                    $us->delete();
                } else {
                    unset($newList[$us->user_id]);
                }
            }
            foreach ($newList as $newUserID => $key) {
                UserShift::create([
                    'user_id' => $newUserID,
                    'shift_id' => $id
                ]);
            }

            $shift->project_name = $request->input('project_name');
            $shift->project_name = $request->input('project_number');
            $shift->timeStart = $request->input('timeStart');
            $shift->timeEnd = $request->input('timeEnd');
            $shift->date = $request->input('date');

            $shift->save();

            return response()->json([
                "Request" => 'Shift successfully updated'
            ]);
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function shiftDelete(Request $request)
    {
        $id = $request['shift_id'];
        if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
            if (Shift::where('id', $id)) {
                Shift::find($id)->delete();
                UserShift::where('shift_id', $id)->delete();
                return response()->json([
                    'Request' => 'Shift deleted successfully',
                ]);
            } else {
                return response()->json([
                    'Error' => 'There is no such Shift.',
                ]);
            }
        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }

    public function deleteShiftWorker(Request $request)
    {
        $id = $request['shift_id'];
        $idUser = $request['user_id'];
        if (User::where('user_token', $request->input('token'))->exists() && $request->input('token') == true) {
            if (UserShift::where('shift_id', $id)->where('user_id', $idUser)->exists()) {
                UserShift::where('shift_id', $id)->where('user_id', $idUser)->delete();
                return response()->json([
                    'Error' => 'All ok',
                ]);
            } else {
                echo 'wwer';
            }


        } else {
            return response()->json([
                'Error' => 'Your token not found',
            ]);
        }
    }


    //Send Email

    public function sendEmail(Request $request)
    {
        $details = [
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body')
        ];
        Mail::to($request->input('email'))->send(new Email($details));
        return response()->json([
            'Request' => 'Email was sent'
        ]);
    }


    public function chat(Request $request, $id = null)
    {
        $messages = [];
        $otherUser = null;
        $user_id = \Auth::id();
        if ($id) {
            $otherUser = User::findorfail($id);
            $otherUsername = $otherUser->first_name;
            $group_id = (\Auth::id() > $id) ? \Auth::id() . '.' . $id : $id . '.' . \Auth::id();
            $messages = Notification::where('group_id', $group_id)->get()->toArray();
            Notification::where(['from_user' => $id, 'to_user' => $user_id, 'is_read' => 0])->update(['is_read' => 1]);
        }
        $token = \Auth::user()->user_token;
        $friends = User::where('user_token', '!=', $token)->select('*', DB::raw("(SELECT count(id) from notification where notification.to_user=$user_id and notification.from_user=users.id and is_read=0) as unread_messages"))->get()->toArray();

        return view('pages/chat', compact('friends', 'messages', 'otherUser', 'id', 'user_id'));
    }

    public function shiftRequest(Request $request)
    {

        $rules = [
            'shift_id' => 'required|exists:usershifts,shift_id',
            'user_id' => 'required|exists:usershifts,user_id',
        ];
        $messages = [
            'shift_id.required' => 'Please write your shift ID',
            'shift_id.exists' => 'This Shift ID does not exist',
            'user_id.required' => 'Enter your user ID',
            'user_id.exists' => 'You entered an incorrect user ID'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = $validator->errors();

        $user_id = \Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'errors' => $errors->first()
            ]);
        } else {
            if (User::where('user_token', '=', $request['token'])->exists() && $request['token'] == true) {
                if (\Auth::check() == $user_id) {
                    if(UserShift::where(['shift_id' => $request['shift_id'], 'user_id' => $request['user_id']])->exists()){
                        UserShift::where([
                            'user_id' => $user_id,
                            'shift_id' => $request['shift_id'],
                            'user_id' => $request['user_id']])->update(['shift_request' => $request['shift_request']]);
                        return response()->json([
                            'Error' => 'Saved',
                        ]);
                    } else{
                        return response()->json([
                            'Error' => 'Worker or shift not found',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'Error' => 'Your token not found',
                ]);
            }
        }
    }

}
