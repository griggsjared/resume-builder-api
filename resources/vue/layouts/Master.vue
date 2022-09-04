<script lang="ts" setup>
  import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
  <nav id="header" class="fixed top-0 z-10 w-full bg-white shadow">
    <div class="container flex flex-wrap items-center w-full pt-3 pb-3 mx-auto mt-0">
      <div class="w-1/2 pl-2 md:pl-0">
        <Link href="/" class="text-base font-bold no-underline text-green xl:text-xl hover:no-underline" >
          Resume Builder
        </Link>
      </div>
      <div class="w-1/2 pr-0">
        <div class="relative flex justify-end">
          <div class="relative text-sm">
            <button type="button" class="flex items-center focus:outline-none">
              <span class="hidden md:inline-block">Hi, Jared</span>
            </button>
            <div class="absolute top-0 right-0 z-30 invisible min-w-full mt-2 mt-12 overflow-auto bg-white rounded shadow-md">
              <ul class="list-reset">
                <li><Link href="/logout" class="block px-4 py-2 text-gray-900 no-underline hover:bg-gray-400 hover:no-underline">Logout</Link></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <div class="container w-full pt-20 mx-auto">
    <div class="w-full px-4 mb-16 leading-normal text-gray-800 md:px-0 md:mt-8">
      <slot />
    </div>
  </div>
</template>
