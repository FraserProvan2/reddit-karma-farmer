<template>
  <div class="card">
    <div class="card-header">Logs</div>
    <div class="card-body">
      <ul v-if="this.logs.length > 0" class="list-group">
        <li
          v-for="(log, index) in logs"
          :key="index"
          class="list-group-item text-white p-2 pl-3"
          v-bind:class="{'bg-success':(log.status == 'success'), 'bg-danger':(log.status == 'error')}"
        >{{ log.log }}</li>
      </ul>
      <div v-else class="small text-muted text-center p-1">Nothing to show...</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      logs: []
    };
  },
  mounted() {
    events.$on("log", log_data => {
      this.logs.push({
        status: log_data.status,
        log: `${log_data.time} ${log_data.message}`
      });
    });
  }
};
</script>