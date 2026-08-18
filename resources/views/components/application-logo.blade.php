<img src="{{ asset('images/logo/logo-hadirin.png') }}" alt="HADIRin Logo" {{ $attributes->merge(['class' => 'h-9 w-auto object-contain transition-all duration-200 app-logo-img']) }}>

<style>
.app-logo-img {
    filter: invert(1) invert(18%) sepia(60%) saturate(500%) hue-rotate(195deg) brightness(75%) contrast(120%);
}
html.dark .app-logo-img,
.dark .app-logo-img {
    filter: none;
    opacity: 0.95;
}
</style>
