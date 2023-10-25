<template>
    <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
            <h3 class="intro-y text-lg font-medium">
                <slot name="title"></slot>
            </h3>

            <p class="mt-1 text-sm text-gray-600">
                <slot name="description"></slot>
            </p>
        </div>
    </div>
</template>
