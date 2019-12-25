<template>
  <div class="card">
    <div class="card-body">
      <!-- Timer -->
      <h6 class="text-center w-100">
        Session:
        <span class="text-primary">00:00:00</span>
      </h6>

      <!-- Start/Stop -->
      <a
        v-if="!process_running"
        @click="this.toggleProccessRunning"
        class="btn btn-lg btn-success text-white w-100"
      >Start</a>
      <a
        v-else
        @click="this.toggleProccessRunning"
        class="btn btn-lg btn-danger text-white w-100"
      >Stop</a>

      <div
        class="small text-secondary text-center w-100 mt-2"
      >{{ this.posted_this_session }} Post(s)</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      process_running: false,
      posted_this_session: 0,
      time_to_wait: 0
    };
  },

  methods: {
    toggleProccessRunning() {
      this.process_running = !this.process_running;

      if (!this.process_running) {
        this.posted_this_session = 0; // reset post count
      }

      this.runtime(); // start process
    },

    runtime() {
      // randomly choose wait time
      this.time_to_wait = this.random(1200 * 1000, 2100 * 1000); // 20 - 35 mins

      if (!this.process_running) {
        return;
      }

      window.axios.get("/run").then(response => {
        // update posts this turn
        if (response.data.status === "success") {
          this.posted_this_session = ++this.posted_this_session;
        }

        // log post success
        events.$emit("log", {
          status: response.data.status,
          message: response.data.message,
          time: new Date().toLocaleTimeString()
        });

        // log waiting time
        events.$emit("log", {
          status: "info",
          message: `Waiting for ${this.miliToSec(this.time_to_wait)}`,
          time: new Date().toLocaleTimeString()
        });
      });

      // wait
      setTimeout(() => {
        this.runtime();
      }, this.time_to_wait);
    },

    /**
     * Utility Time keeping
     */

    random(min, max) {
      return Math.floor(Math.random() * (max - min + 1) + min);
    },

    miliToSec(millis) {
      let minutes = Math.floor(millis / 60000);
      let seconds = ((millis % 60000) / 1000).toFixed(0);
      return minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    }
  }
};
</script>
