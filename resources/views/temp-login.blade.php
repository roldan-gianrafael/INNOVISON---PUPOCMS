<div style="padding: 50px; text-align: center; font-family: sans-serif;">
    <h2>Clinic System: Temporary Login</h2>
    <p>Piliin ang account na gagamitin para sa testing:</p>
    
    @foreach(\App\Models\User::where('is_admin', 0)->get() as $student)
        <a href="/dev-login/{{ $student->id }}" 
           style="display:block; margin: 10px; padding: 15px; background: #8B0000; color: white; text-decoration: none; border-radius: 8px;">
           Login as: {{ $student->name }} ({{ $student->student_id }})
        </a>
    @endforeach
    
    <hr>
    <a href="/admin/walkin" style="color: blue;">Punta sa Admin/Walk-in Side →</a>
</div>