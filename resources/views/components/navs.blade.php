 <!-- Sidebar Navigation -->
 <div class="hidden md:flex w-64 flex-col fixed inset-y-0 z-50 bg-white border-r">
   <div class="flex h-14 items-center border-b px-4">
     <h1 class="text-lg font-semibold">Baclaran Church VMS</h1>
   </div>

   <div class="flex-1 overflow-auto py-2">
     <nav class="grid gap-1 px-2">
       @php
       function navActive($path) {
       return request()->is($path) ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-500 hover:text-white';
       }
       @endphp

       <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('dashboard') }}">
         <!-- Home Icon -->
         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3a2 2 0 01-2-2z" />
         </svg>
         <span>Dashboard</span>
       </a>

       <a href="{{ url('/volunteers') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('volunteers') }}">
         <!-- Users Icon -->
         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h5v-2a4 4 0 00-3-3.87M9 20V10a4 4 0 00-8 0v10m12-10a4 4 0 018 0v10" />
         </svg>
         <span>Volunteers</span>
       </a>

       <a href="{{ url('/schedule') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('schedule') }}">
         <!-- Calendar Icon -->
         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
         </svg>
         <span>Schedule</span>
       </a>

       <a href="{{ url('/tasks') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('tasks') }}">
         <!-- List Icon -->
         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M9 5l7 7-7 7" />
         </svg>
         <span>Tasks</span>
       </a>
     </nav>
   </div>
 </div>

 <div class="flex flex-col flex-1 md:pl-64">
   <!-- Header -->
   <header class="sticky top-0 z-40 flex h-14 items-center gap-4 border-b bg-white px-4 sm:px-6">
     <div class="flex flex-1 items-center justify-end">
       <div class="flex items-center gap-4">
         <button class="p-2 border rounded">
           <!-- Bell Icon -->
           <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
           </svg>
         </button>
         <div class="w-8 h-8 rounded-full overflow-hidden">
           <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin" alt="Admin">
         </div>
       </div>
     </div>
   </header>