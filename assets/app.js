/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

import Vue from "vue";

new Vue({
  el: "#app",
  data() {
    return {
      pokemonUrl:
        "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/",
    };
  },
  methods: {
    getInfo(index) {
      window.location.replace(`/${index}`);
    },
    getUrl(index) {
      if (this.togglePokemon) {
        this.pokemonUrl += `${index}.png`;
      }
    },
  },
  delimiters: ["${", "}$"],
});


