import { defineConfig } from "vite";
import path from "path";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import preprocess from "svelte-preprocess";

export default defineConfig({
  resolve: {
    alias: {
      $lib: path.resolve(__dirname, "./ol/src/lib"),
    },
  },
  plugins: [
    svelte({
      preprocess: preprocess(),
    }),
  ],
  build: {
    sourcemap: false,
    // generate manifest.json in outDir
    manifest: false,
    rollupOptions: {
      // overwrite default .html entry
      input: ["./ol/src/admin.ts"],
      output: {
        assetFileNames: "[name].admin.[ext]",
      },
    },
    outDir: "./assets/ol",
    emptyOutDir: false,
    lib: {
      entry: path.resolve(__dirname, "ol/src/admin.ts"),
      name: "bwMap",
      formats: ["umd"],
      fileName: (format) => `bw-map.admin.${format}.js`,
    },
  },
});
