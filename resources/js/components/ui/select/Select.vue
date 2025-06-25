<script setup lang="ts">
import { Icon } from '@iconify/vue'
import {
    type NavigationMenuRootProps,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectLabel,
    SelectPortal,
    SelectRoot,
    SelectScrollDownButton,
    SelectScrollUpButton,
    SelectTrigger,
    SelectValue,
    SelectViewport
} from 'reka-ui';
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<NavigationMenuRootProps & {
    defaultValue?: string | number
    modelValue?: string | number
    label: string
    options: string[]
    class?: HTMLAttributes['class']
}>(), {})

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
})

</script>

<template>
    <SelectRoot v-model="modelValue" :class="cn(props.class,)">
        <SelectTrigger
            class="inline-flex min-w-[160px] items-center justify-between rounded-lg px-[15px] text-sm leading-none h-[35px] gap-[5px] bg-white hover:bg-stone-50 border shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black data-[placeholder]:text-green9 outline-none"
            :class="[modelValue ? 'text-grass11' : 'text-muted-foreground']"
            aria-label="Customise options"
        >
            <SelectValue :placeholder="props.label" />
            <Icon
                icon="radix-icons:chevron-down"
                class="h-3.5 w-3.5"
            />
        </SelectTrigger>

        <SelectPortal>
            <SelectContent
                class="min-w-[160px] bg-white rounded-lg border shadow-sm will-change-[opacity,transform] data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100]"
                :side-offset="5"
            >
                <SelectScrollUpButton class="flex items-center justify-center h-[25px] bg-white text-violet11 cursor-default">
                    <Icon icon="radix-icons:chevron-up" />
                </SelectScrollUpButton>

                <SelectViewport class="p-[5px]">
                    <SelectLabel class="px-[25px] text-sm leading-[25px] text-muted-foreground">
                        {{ props.label }}
                    </SelectLabel>
                    <SelectGroup>
                        <SelectItem
                            v-for="(option, index) in props.options"
                            :key="index"
                            class="text-sm leading-none text-grass11 rounded-[3px] flex items-center h-[25px] pr-[35px] pl-[25px] relative select-none data-[disabled]:text-mauve8 data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-green9 data-[highlighted]:text-green1"
                            :value="option"
                            :disabled="option === 'Courgette'"
                        >
                            <SelectItemIndicator class="absolute left-0 w-[25px] inline-flex items-center justify-center">
                                <Icon icon="radix-icons:check" />
                            </SelectItemIndicator>
                            <SelectItemText class="text-sm">
                                {{ option }}
                            </SelectItemText>
                        </SelectItem>
                    </SelectGroup>
                </SelectViewport>

                <SelectScrollDownButton class="flex items-center justify-center h-[25px] bg-white text-violet11 cursor-default">
                    <Icon icon="radix-icons:chevron-down" />
                </SelectScrollDownButton>
            </SelectContent>
        </SelectPortal>
    </SelectRoot>
</template>
