import { defineConfig } from "vite";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import preprocess from "svelte-preprocess";
import path from "path";

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
    sourcemap: true,
    // generate manifest.json in outDir
    manifest: false,
    rollupOptions: {
      // overwrite default .html entry
      input: ["./ol/src/main.ts", "./ol/src/admin.ts"],
    },
  },
});
