<?php
namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Storage;

class JobboardController extends Controller
{

    public function activateresume($resumeid)
    {
      $user_id = Auth::user()->id;
      Candidate::where('user_id',$user_id)
                ->update(['active' => 0]);

      $job = Candidate::find($resumeid);

      $job->active = 1;

      $job->save();

      return redirect()->back();
    }
    public function activate($jobid)
    {
      $job = Job::find($jobid);

      $job->active = !$job->active;

      $job->save();

      return redirect()->back();
    }
    public function jobsposted()
    {
      $userid = Auth::user()->id;

      $jobs = Job::where('user_id',$userid)->orderBy('id', 'DESC')->get();

      return view('jobsposted',["jobs" => $jobs]);

    }
    public function resumesposted()
    {
      $userid = Auth::user()->id;

      $candidates = Candidate::where('user_id',$userid)->orderBy('id', 'DESC')->get();

      return view('resumesposted',["candidates" => $candidates]);

    }
    public function jobsapplied()
    {
      $userid = Auth::user()->id;

      $jobs = User::find($userid)->jobs()->orderBy('id', 'DESC')->get();

      return view('jobsapplied',["jobs" => $jobs]);

    }
    public function jobapplications($id)
    {
      $userid = Auth::user()->id;

      $jobs = User::find($userid);

      $application = \DB::table('candidates')
            ->join('job_user', 'candidates.user_id', '=', 'job_user.user_id')
            ->where('job_id',$id)
            ->where('active',1)
            ->select('candidates.*', 'candidates.id as can_id')
            ->get();

      return view('jobapplications',["candidates" => $application]);

    }
    public function apply($jobid){

      $job = Job::find($jobid);

      $userid = Auth::user()->id;

      $job->users()->syncWithoutDetaching([$userid]);

      return redirect()->back();

    }
    public function postresume(){


      return view('postresume');

    }
    public function postjob(){

      return view('postjob');

    }

    public function editresume($id){

      $userid = Auth::user()->id;

      $can = Candidate::find($id);

      $name = explode(" ",$can->profile_name);

      $size = count($name);

      if($size == 1)
      $name[1] = $name[2] = '';
      elseif($size == 2) {
        $name[2] = '';
      }

      return view('editresume',["can" => $can,'name' => $name]);

    }
    public function editjob($id){

      $userid = Auth::user()->id;

      $job = Job::find($id);

      return view('editjob',["job" => $job]);

    }
    public function updateresumepost(Request $request){
      $path = "";

      if (\Request::hasFile('profile_picture'))
      {
      $this->validate($request, [
          'profile_picture' => 'required|max:1024|mimes:jpeg,png'
      ]);

      $image = \Request::file('profile_picture');

      $filename  = time() . '.' . $image->getClientOriginalExtension();

      $path = public_path('assets/images/candidates/' . $filename);
    }
    $this->validate($request, [
        'first_name' => 'required',
        'profile_title' => 'required',
        'address' => 'required',
        'description' => 'required',
        'skills' => 'required'
    ]);

    $id = $request->input('id');

    $can = Candidate::find($id);
    //$old_path = public_path('assets/' . $can->profile_pic);
    $can->profile_name = trim($request["first_name"]." ".$request->input('middle_name', '')." ".$request->input('last_name', ''));
    $can->profile_title = $request["profile_title"];
    $can->address = trim($request["address"]." ".$request->input('city', '')." ".$request->input('country', ''));
    $can->profile_description = $request["description"];
    $can->skills = $request["skills"];
    if($path == "")
    $can->profile_pic = $can->profile_pic;
    else{
      Image::make($image->getRealPath())->resize(72, 72)->save($path);
      $can->profile_pic = 'images/candidates/'.$filename;
    }
    $can->save();


      return redirect()->back();
    }
    public function updatejobpost(Request $request){

      $path = "";

      if (\Request::hasFile('company_logo'))
      {
      $this->validate($request, [
          'company_logo' => 'required|max:1024|mimes:jpeg,png'
      ]);

      $image = \Request::file('company_logo');

      $filename  = time() . '.' . $image->getClientOriginalExtension();

      $path = public_path('assets/images/company-logo/' . $filename);
    }

    $this->validate($request, [
        'job_title' => 'required',
        'job_description' => 'required',
        'job_location' => 'required',
        'job_type' => 'required',
        'job_category' => 'required',
        'company_name' => 'required'
    ]);

    $job = Job::find($id);
    $job->user_id = Auth::user()->id;
    $job->job_title = $request["job_title"];
    $job->job_description = $request["job_description"];
    $job->job_location = $request["job_location"];
    $job->job_type = $request["job_type"];
    $job->payment = 200;
    $job->job_category = $request["job_category"];
    $job->company_name = $request["company_name"];
    if($path == "")
    $job->company_logo = $job->company_logo;
    else{
      Image::make($image->getRealPath())->resize(72, 72)->save($path);
      $job->company_logo = 'images/company-logo/'.$filename;
    }
    $job->save();

      return redirect()->back();
    }

    public function saveresumepost(Request $request){

      $this->validate($request, [
          'profile_picture' => 'required|max:1024|mimes:jpeg,png',
          'first_name' => 'required',
          'profile_title' => 'required',
          'address' => 'required',
          'description' => 'required',
          'skills' => 'required'
      ]);

      if (\Request::hasFile('profile_picture'))
      {
      $image = \Request::file('profile_picture');

      $filename  = time() . '.' . $image->getClientOriginalExtension();

      $path = public_path('assets/images/candidates/' . $filename);
          $user_id = Auth::user()->id;
          Image::make($image->getRealPath())->resize(72, 72)->save($path);
          $can = new Candidate;
          $can->user_id = $user_id;
          $can->profile_name = trim($request["first_name"]." ".$request->input('middle_name', '')." ".$request->input('last_name', ''));
          $can->profile_title = $request["profile_title"];
          $can->address = trim($request["address"]." ".$request->input('city', '')." ".$request->input('country', ''));
          $can->profile_description = $request["description"];
          $can->skills = $request["skills"];
          $can->profile_pic = 'images/candidates/'.$filename;
          $can->active = true;
          $can->save();

          Candidate::where('id','!=',$can->id)
                    ->where('user_id',$user_id)
                    ->update(['active' => 0]);
      }
      return view('postresume');
    }
    public function savejobpost(Request $request){

      $this->validate($request, [
          'job_title' => 'required',
          'job_description' => 'required',
          'job_location' => 'required',
          'job_type' => 'required',
          'job_category' => 'required',
          'company_name' => 'required',
          'company_logo' => 'required|max:1024|mimes:jpeg,png',
      ]);


          if (\Request::hasFile('company_logo'))
          {
          $image = \Request::file('company_logo');

          $filename  = time() . '.' . $image->getClientOriginalExtension();

          $path = public_path('assets/images/company-logo/' . $filename);

              Image::make($image->getRealPath())->resize(72, 72)->save($path);
              $job = new Job;
              $job->user_id = Auth::user()->id;
              $job->job_title = $request["job_title"];
              $job->job_description = $request["job_description"];
              $job->job_location = $request["job_location"];
              $job->job_type = $request["job_type"];
              $job->payment = 200;
              $job->job_category = $request["job_category"];
              $job->company_name = $request["company_name"];
              $job->company_logo = 'images/company-logo/'.$filename;
              $job->save();
          }

      return view('postjob');

    }
    public function home()
    {
      $ufjobs = Job::where('featured',false)->orderBy('id', 'DESC')->get();

      $fjobs = Job::where('featured',true)->orderBy('id', 'DESC')->get();

      return view('landing',["ufjobs" => $ufjobs,"fjobs" => $fjobs]);

    }
     public function candidates()
    {
        $candidates = Candidate::all();

        return view('candidate',["candidates" => $candidates]);
    }
    public function jobdetails($id){

       $job = Job::find($id);

       return view('jobdetails',["job" => $job]);
    }
    public function candidatedetails($id){

       $can = Candidate::find($id);

       return view('candidatedetails',["can" => $can]);
    }
    public function candidatesearch(Request $request)
    {
      $builder = Candidate::query();
      if ($request->has('keyword')) {
          $queryString = $request->input('keyword');
          $builder->where('skills', 'LIKE', "%$queryString%");
      }
      if ($request->has('location')) {
          $queryString = $request->input('location');

          $builder->where(function ($query) use ($queryString)  {
                $query->where('address', 'LIKE', "% $queryString %")
                      ->orWhere('address', 'LIKE', "$queryString %")
                      ->orWhere('address', 'LIKE', "% $queryString");
            });
      }
      if ($request->has('category')) {
          $queryString = $request->input('category');
          $builder->where('profile_title', 'LIKE', "%$queryString%");
      }

      $candidates = $builder  ->orderBy('id', 'DESC')->get();


      return view('candidate',["candidates" => $candidates]);
    }
    public function jobsearch(Request $request)
    {
      $builder = Job::query();
      $builder->where('active',true);
      if ($request->has('keyword')) {
          $queryString = $request->input('keyword');
          $builder->where('job_title', 'LIKE', "%$queryString%");
      }
      if ($request->has('location')) {
          $queryString = $request->input('location');

          $builder->where(function ($query) use ($queryString)  {
                $query->where('job_location', 'LIKE', "% $queryString %")
                      ->orWhere('job_location', 'LIKE', "$queryString %")
                      ->orWhere('job_location', 'LIKE', "% $queryString");
            });
      }
      if ($request->has('category')) {
          $queryString = $request->input('category');
          $builder->where('job_category', 'LIKE', "%$queryString%");
      }

      $jobs = $builder->orderBy('id', 'DESC')->get();


      return view('search',["jobs" => $jobs]);

    }

    public function jobboard()
    {
      $ufjobs = Job::where('active',true)->where('featured',false)->orderBy('id', 'DESC')->get();

      $fjobs = Job::where('active',true)->where('featured',true)->orderBy('id', 'DESC')->get();

      return view('job',["ufjobs" => $ufjobs,"fjobs" => $fjobs]);

    }
    public function contact()
    {
      return view('contact');
    }
    public function sendcontactmail(Request $input){


      $toEmail = "contact@afrologix.com";
      $toName = "Admin";

        $fromEmail = $input["email"];
        $fromName = $input["name"];

        /*  $this->validate($input, [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|max:255'
      ]);*/

            $validator = Validator::make($input->all(), [
              'name' => 'required|max:255',
              'email' => 'required|email|max:255',
              'message' => 'required|max:255'
           ]);

           if ($validator->fails()) {

               return json_encode(['responseText' => 'Opps something went wrong']);

           }


        $data = array('messagetxt' => $input["message"],'fromName' => $fromName ,'fromEmail' => $fromEmail);
        \Mail::send('mail', $data , function ($message) use ($fromEmail, $fromName, $toEmail, $toName) {
            $message->from($fromEmail, $fromName);
            $message->to($toEmail, $toName)->subject('Contact message for job');
        });

      return json_encode(['responseText' => 'Message send successfully']);
    }

}
