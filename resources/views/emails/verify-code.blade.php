<p>Hello {{ ucwords($name) }},</p>

<p>Welcome! Please verify your account using the code below:</p>

<h2>{{ $code }}</h2>

<p>This code expires in 10 minutes.</p>

<p>Thanks,<br>Team {{ config('app.name') }}</p>
