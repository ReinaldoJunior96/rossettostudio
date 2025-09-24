module.exports = {
  content: [
    './*.php',
    './**/*.php',
    './woocommerce/**/*.php',
    './template-parts/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        purple: {
          DEFAULT: '#6A0DAD', // Roxo predominante
          light: '#9B59B6', // Roxo claro
        },
        yellow: {
          DEFAULT: '#FFCC00', // Amarelo vibrante
          light: '#FFE066', // Amarelo claro
        },
        black: '#000000', // Preto
        white: '#FFFFFF', // Branco
        gray: {
          DEFAULT: '#808080', // Cinza médio
          light: '#D3D3D3', // Cinza claro
        },
      },
    },
  },
  plugins: [],
  // (Opcional) safelist para classes dinâmicas:
  // safelist: [{ pattern: /(bg|text|border)-(red|blue|green|gray)-(100|200|500|700)/ }, { pattern: /grid-cols-(1|2|3|4|6|12)/ }],
};