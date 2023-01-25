<x-mail::message>

# Please do not share otp code to other.

Otp Code is <span style="font-weight: bolder;font-size:xx-large">{{ $otp->otp_code }}</span> .Its Expires in 15 minutes.

{{-- <x-mail::button :url="''">

</x-mail::button> --}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
