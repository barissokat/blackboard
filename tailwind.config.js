module.exports = {
    purge: [],
    theme: {
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
        },
        fontFamily: {
            display: ['Gilroy', 'sans-serif'],
            body: ['Graphik', 'sans-serif'],
        },
        borderWidth: {
            default: '1px',
            '0': '0',
            '2': '2px',
            '4': '4px',
        },
        extend: {
            colors: {
                grey: {
                    default: 'rgba(0, 0, 0, 0.4)',
                    lighter: '#F5F6F9',
                },
                white: {
                    default: '#fff',
                },
                blue: {
                    default: '#47cdff',
                    light: '#8ae2fe',
                },
            },
            spacing: {
                '96': '24rem',
                '128': '32rem',
            }
        }
    },
    variants: {},
    plugins: [],
}
