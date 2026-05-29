{{-- Shared CSS for the password-flow pages (email / reset / confirm).
     Same design language as auth/login.blade.php so the whole auth flow
     feels cohesive — warm dark background, gold accents, frosted card,
     full dark-mode parity. --}}
<style>
    .gp-auth {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5.5rem 1.25rem 3rem;
        background:
            radial-gradient(120% 80% at 20% 10%, rgba(212, 160, 60, 0.30), transparent 60%),
            radial-gradient(120% 80% at 90% 90%, rgba(232, 90, 90, 0.16), transparent 60%),
            linear-gradient(135deg, #1a1208 0%, #2a1c10 55%, #0c0c10 100%);
        position: relative;
    }
    .gp-auth::before {
        content: "";
        position: absolute;
        inset: 0;
        background: url('{{ asset("images/background.svg") }}') center / 520px auto repeat;
        opacity: .06;
        pointer-events: none;
    }

    .gp-auth__card {
        position: relative;
        width: 100%;
        max-width: 440px;
        background: rgba(20, 18, 14, .78);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(212, 160, 60, .35);
        border-radius: 18px;
        padding: 2.5rem 2rem 2.25rem;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, .55),
            0 4px 14px rgba(0, 0, 0, .35);
    }

    .gp-auth__brand {
        display: flex;
        align-items: center;
        gap: .65rem;
        justify-content: center;
        margin-bottom: 1.25rem;
        color: #fbf3e1;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.2rem;
        font-weight: 700;
        text-decoration: none;
    }
    .gp-auth__brand span.accent { color: #f3c779; }
    .gp-auth__mark {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: radial-gradient(circle at 50% 45%, #fbf3e1 0%, #f3d99a 70%, #d4a03c 100%);
        box-shadow: 0 0 0 1px rgba(243, 199, 121, .45), 0 0 14px 1px rgba(212, 160, 60, .35);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .gp-auth__mark img { height: 32px; width: auto; display: block; }

    .gp-auth__icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(212, 160, 60, .25), rgba(184, 135, 46, .12));
        border: 1px solid rgba(212, 160, 60, .45);
        color: #f3c779;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin: 0 auto 1rem;
    }

    .gp-auth__title,
    html[data-theme="dark"] .gp-auth__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.55rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 .35rem;
        text-align: center;
    }
    .gp-auth__subtitle {
        color: rgba(251, 243, 225, .65);
        font-size: .92rem;
        text-align: center;
        margin: 0 0 1.6rem;
        line-height: 1.5;
    }

    .gp-auth__status {
        background: rgba(37, 211, 102, .12);
        border: 1px solid rgba(37, 211, 102, .35);
        color: #6ff09a;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: .85rem;
        margin-bottom: 1.1rem;
        display: flex;
        align-items: flex-start;
        gap: .5rem;
    }

    .gp-auth__field { margin-bottom: 1.1rem; }
    .gp-auth__field label {
        display: block;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: .4rem;
        color: rgba(251, 243, 225, .75);
    }

    .gp-auth__input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .gp-auth__input-wrap > i.gp-auth__input-icon {
        position: absolute;
        left: 14px;
        color: rgba(251, 243, 225, .55);
        font-size: .92rem;
        pointer-events: none;
    }
    .gp-auth__input {
        width: 100%;
        padding: .8rem .85rem .8rem 2.4rem;
        background: rgba(12, 12, 16, .65);
        border: 1px solid rgba(212, 160, 60, .25);
        border-radius: 10px;
        font-size: .98rem;
        color: #fbf3e1;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .gp-auth__input::placeholder { color: rgba(251, 243, 225, .35); }
    .gp-auth__input:focus {
        outline: none;
        border-color: #d4a03c;
        box-shadow: 0 0 0 4px rgba(212, 160, 60, .18);
        background: rgba(12, 12, 16, .85);
    }
    .gp-auth__input.is-invalid {
        border-color: #d9534f;
        box-shadow: 0 0 0 4px rgba(217, 83, 79, .12);
    }

    .gp-auth__error {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: #ff8a87;
        font-size: .8rem;
        margin-top: .4rem;
    }

    .gp-auth__submit {
        width: 100%;
        background: linear-gradient(135deg, #d4a03c, #b8872e);
        color: #1a1208;
        border: 0;
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        cursor: pointer;
        margin-top: .35rem;
        box-shadow: 0 10px 24px rgba(184, 135, 46, .35);
        transition: transform .12s ease, box-shadow .15s ease, filter .15s ease;
    }
    .gp-auth__submit:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
        box-shadow: 0 14px 30px rgba(184, 135, 46, .42);
    }
    .gp-auth__submit:active { transform: translateY(0); }

    .gp-auth__link {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: #f3c779;
        text-decoration: none;
        font-size: .9rem;
        font-weight: 600;
        margin-top: 1.25rem;
        justify-content: center;
        width: 100%;
    }
    .gp-auth__link:hover { color: #ffd97a; text-decoration: underline; }

    @media (max-width: 480px) {
        .gp-auth__card { padding: 2rem 1.5rem 1.75rem; }
        .gp-auth__title { font-size: 1.35rem; }
    }
</style>
