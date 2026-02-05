<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    institution: Object,
});

const form = useForm({
    name: props.institution.name,
    code: props.institution.code,
    description: props.institution.description || "",
});

const submit = () => {
    form.put(route("admin.institutions.update", props.institution.id));
};
</script>

<template>
    <Head title="Edit Lembaga" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Lembaga
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <header class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Lembaga</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Perbarui informasi lembaga.
                                </p>
                            </div>
                            <Link :href="route('admin.institutions.index')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 font-bold text-sm">
                                &larr; Kembali
                            </Link>
                        </header>

                        <form @submit.prevent="submit" class="max-w-xl">
                            <div class="mb-4">
                                <InputLabel for="name" value="Nama Lembaga" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="code" value="Kode Unit" />
                                <TextInput
                                    id="code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.code"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.code" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="description" value="Deskripsi (Opsional)" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="flex items-center gap-4 mt-8">
                                <PrimaryButton :disabled="form.processing">
                                    Simpan Perubahan
                                </PrimaryButton>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Tersimpan.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
