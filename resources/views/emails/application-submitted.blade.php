<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $forAdmin ? __('New application') : __('Application received') }}</title>
</head>
<body style="margin:0;background:#f5f7f3;color:#203431;font-family:Arial,sans-serif;line-height:1.6;">
    <div style="max-width:620px;margin:0 auto;padding:32px 18px;">
        <div style="padding:26px;border:1px solid #d9e3de;border-radius:10px;background:#fff;">
            <p style="margin:0 0 12px;color:#59796e;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">ABRUGIS</p>
            <h1 style="margin:0 0 18px;font-family:Georgia,serif;font-size:28px;font-weight:400;">
                {{ $forAdmin ? __('New application') : __('Application received') }}
            </h1>
            @if ($forAdmin)
                <p>{{ __('A new client application has been submitted.') }}</p>
            @else
                <p>{{ __('Thank you. We have received your application and will contact you soon.') }}</p>
            @endif
            <div style="margin-top:24px;padding-top:18px;border-top:1px solid #d9e3de;">
                <p style="margin:0 0 7px;"><strong>{{ __('Name') }}:</strong> {{ $pieteikums->client_name }}</p>
                <p style="margin:0 0 7px;"><strong>{{ __('Email') }}:</strong> {{ $pieteikums->client_email }}</p>
                <p style="margin:0 0 7px;"><strong>{{ __('Phone number') }}:</strong> {{ $pieteikums->client_phone }}</p>
                @if ($pieteikums->requested_date)
                    <p style="margin:0 0 7px;"><strong>{{ __('Preferred date') }}:</strong> {{ $pieteikums->requested_date->format('d.m.Y') }}</p>
                @endif
                <p style="margin:16px 0 0;"><strong>{{ __('Project description') }}:</strong><br>{{ $pieteikums->project_description }}</p>
            </div>
        </div>
    </div>
</body>
</html>
