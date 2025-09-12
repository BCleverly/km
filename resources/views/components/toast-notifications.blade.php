<!-- Global notification live region, render this permanently at the end of the document -->
<div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50">
  <div class="flex w-full flex-col items-center space-y-4 sm:items-end" x-data="toastNotifications()" x-init="init()">
    <!-- Toast notifications will be dynamically inserted here -->
    <template x-for="toast in toasts" :key="toast.id">
      <div 
        x-show="toast.visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-x-2 sm:translate-y-0"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave-end="translate-y-2 opacity-0 sm:translate-x-2 sm:translate-y-0"
        class="pointer-events-auto w-full max-w-sm transform rounded-lg bg-white shadow-lg outline-1 outline-black/5 dark:bg-gray-800 dark:-outline-offset-1 dark:outline-white/10"
        :class="{
          'border-l-4 border-green-400': toast.type === 'success',
          'border-l-4 border-red-400': toast.type === 'error',
          'border-l-4 border-yellow-400': toast.type === 'warning',
          'border-l-4 border-blue-400': toast.type === 'info'
        }"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="shrink-0">
              <!-- Success Icon -->
              <svg x-show="toast.type === 'success'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-green-400">
                <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              
              <!-- Error Icon -->
              <svg x-show="toast.type === 'error'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-red-400">
                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              
              <!-- Warning Icon -->
              <svg x-show="toast.type === 'warning'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-yellow-400">
                <path d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              
              <!-- Info Icon -->
              <svg x-show="toast.type === 'info'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-blue-400">
                <path d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            
            <div class="ml-3 w-0 flex-1 pt-0.5">
              <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="toast.message"></p>
              <p x-show="toast.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="toast.description"></p>
            </div>
            
            <div class="ml-4 flex shrink-0">
              <button 
                type="button" 
                @click="removeToast(toast.id)"
                class="inline-flex rounded-md text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:hover:text-white dark:focus:outline-indigo-500"
              >
                <span class="sr-only">Close</span>
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                  <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</div>

<script>
function toastNotifications() {
  return {
    toasts: [],
    
    init() {
      // Listen for Livewire notify events
      Livewire.on('notify', (data) => {
        this.addToast(data[0]);
      });
    },
    
    addToast(data) {
      const toast = {
        id: Date.now() + Math.random(),
        type: data.type || 'info',
        message: data.message || 'Notification',
        description: data.description || null,
        visible: true,
        duration: data.duration || 5000
      };
      
      this.toasts.push(toast);
      
      // Auto-remove after duration
      setTimeout(() => {
        this.removeToast(toast.id);
      }, toast.duration);
    },
    
    removeToast(id) {
      const index = this.toasts.findIndex(toast => toast.id === id);
      if (index > -1) {
        this.toasts[index].visible = false;
        setTimeout(() => {
          this.toasts.splice(index, 1);
        }, 200); // Wait for transition to complete
      }
    }
  }
}
</script>
