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
            darkest:  "#012A36", // Hijau teal sangat gelap
            darker:   "#014F56", // Teal tua elegan
            medium:   "#027373", // Teal medium, segar & profesional
            light:    "#38A3A5", // Teal terang untuk hover
            lighter:  "#A9D6D6", // Teal pastel lembut
            lightest: "#F2EFE7", // Sangat terang, hampir putih
        },

        primary:   "#027373",   // Teal medium
        secondary: "#014F56",   // Teal tua elegan
    },

    backgroundImage: {
        "brand-gradient":
            `linear-gradient(135deg, #012A36 0%, #014F56 80%, #027373 100%)`,
    },
            // colors: {
            //     azwara: {
            //         darkest:  "#021024",
            //         darker:   "#052659",
            //         medium:   "#5483B3",
            //         light:    "#7DA0CA",
            //         lighter:  "#C1E8FF",
            //         lightest: "#E6F7FF",
            //     },

            //     primary: "#5483B3",
            //     secondary: "#052659",
            // },

            // backgroundImage: {
            //     "brand-gradient":
            //         `linear-gradient(135deg, #021024 0%, #052659 40%, #5483B3 100%)`,
            // },

// colors: {
//     azwara: {
//         darkest:  "#0A0A0A", // Hitam pekat
//         darker:   "#1C1C1C", // Abu gelap elegan
//         medium:   "#2F80ED", // Biru modern
//         light:    "#56CCF2", // Biru terang segar
//         lighter:  "#BBE1FA", // Biru pastel lembut
//         lightest: "#F9FBFF", // Hampir putih
//     },

//     primary:   "#2F80ED",
//     secondary: "#1C1C1C",
// },

// backgroundImage: {
//     "brand-gradient":
//         `linear-gradient(135deg, #0A0A0A 0%, #1C1C1C 50%, #2F80ED 100%)`,
// },



        },
    },

    plugins: [
        require("@tailwindcss/forms"),
    ],
};
