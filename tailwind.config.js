/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/**/*.tsx",
        "./resources/**/*.ts",
        "./resources/**/*.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "ui-sans-serif", "system-ui"],
            },

            colors: {
                azwara: {
                    darkest:  "#021024",
                    darker:   "#052659",
                    medium:   "#5483B3",
                    light:    "#7DA0CA",
                    lighter:  "#C1E8FF",
                    lightest: "#E6F7FF",
                },

                primary: "#5483B3",
                secondary: "#052659",
            },

            backgroundImage: {
                "brand-gradient":
                    `linear-gradient(135deg, #021024 0%, #052659 40%, #5483B3 100%)`,
            },
        },
    },

    plugins: [
        require("@tailwindcss/forms"),
    ],
};
