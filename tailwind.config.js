import preset from "./vendor/filament/support/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    // Se agrega el campo theme para modificar los colores del template
    theme: {
        extend: {
            colors: {
                primary: "#6D28D9", // Morado oscuro
                secondary: "#111827", // Negro
            },
        },
    },
};
