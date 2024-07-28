<template>

    <Head title="Yard" />

    <AuthenticatedLayout>
        <div class="pl-12 pr-4 py-4">
            <PrimaryButton @click.prevent="showModal = true" v-if="permissions.includes('create yard')">Create
            </PrimaryButton>
            <DataTable :data="props.motels.data" :columns="columns" :pagination="props.motels" @limit-query="limitQuery"
                @search-field-query="searchFieldQuery" @delete="deleteData" @edit="editData" @addUser="showModalAddUser"
                :query-limit="props.queryLimit" :route-create="createRoute" :query-name="props.queryName" :action="true"
                :addUser="false" />
        </div>
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-4">
                <div class="">
                    <h6>Create Yard</h6>
                </div>
                <div>
                    <form @submit.prevent="handleSubmit">
                        <InputLabel>Name:</InputLabel>
                        <TextInput type="text" class="mt-1 block w-full" required v-model="formData.yard_name" />
                        <InputLabel>Address:</InputLabel>
                        <TextInput type="text" class="mt-1 block w-full" required v-model="formData.yard_address" />
                        <InputLabel>Status:</InputLabel>
                        <select v-model="formData.status"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">select status...</option>
                            <option value="Active">ACTIVE</option>
                            <option value="Inactive">INACTIVE</option>
                        </select>
                        <div class="flex justify-end">
                            <div>
                                <PrimaryButton>Create</PrimaryButton>
                                <DangerButton @click.prevent="showModal = false">Cancel</DangerButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
        <Modal :show="showModalEdit" @close="showModalEdit = false">
            <div class="p-4">
                <div class="">
                    <h6>Edit Yard</h6>
                </div>
                <div>
                    <form @submit.prevent="handleUpdate">
                        <InputLabel>Name:</InputLabel>
                        <TextInput type="text" class="mt-1 block w-full" required v-model="formData.yard_name" />
                        <InputLabel>Address:</InputLabel>
                        <TextInput type="text" class="mt-1 block w-full" required v-model="formData.yard_address" />
                        <InputLabel>Status:</InputLabel>
                        <select v-model="formData.status"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">select status...</option>
                            <option value="Active">ACTIVE</option>
                            <option value="Inactive">INACTIVE</option>
                        </select>
                        <div class="flex justify-end">
                            <div>
                                <PrimaryButton>Update</PrimaryButton>
                                <DangerButton @click.prevent="showModalEdit = false">Cancel</DangerButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
        <Modal :show="modalAddUser" @close="modalAddUser = false">
            <div class="p-4">
                <div class="">
                    <h6>Add User</h6>
                </div>
                <div>
                    <form @submit.prevent="storeMotelUser">
                        <InputLabel>Name:</InputLabel>
                        <TextInput type="text" class="mt-1 block w-full" required v-model="userFormData.name" />
                        <InputLabel>Email Address:</InputLabel>
                        <TextInput type="email" class="mt-1 block w-full" required v-model="userFormData.email" />
                        <InputLabel>Username:</InputLabel>
                        <TextInput type="text" class="mt-1 block w-full" required v-model="userFormData.username" />
                        <InputLabel>Password:</InputLabel>
                        <TextInput type="password" class="mt-1 block w-full" required v-model="userFormData.password" />
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="userFormData.password_confirmation" required autocomplete="new-password" />
                        <div class="flex justify-end">
                            <div>
                                <PrimaryButton>Create</PrimaryButton>
                                <DangerButton @click.prevent="modalAddUser = false">Cancel</DangerButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DataTable from "@/Components/DataTable.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onMounted, ref, computed } from "vue"
import { router, Head, useForm, usePage } from "@inertiajs/vue3";
import useData from '@/Composables/useData'


const { isLoading, error, storeItem, fetchItem, updateItem, addMotelUser } = useData()
const showModal = ref(false)
const showModalEdit = ref(false)
const modalAddUser = ref(false)
const itemId = ref('')
const motelId = ref()

const page = usePage();
const userRoles = computed(() => page.props.userRoles);
const userPermissions = computed(() => page.props.userPermissions);

const formFields = [
    {
        name: 'name',
        label: 'Name',
        type: 'text',
        select: false
    },

];
const formData = useForm({
    yard_name: '',
    yard_address: '',
    status: ''
})

const userFormData = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: ''
})
const columns = ref([
    { label: 'ID', key: 'id' },
    { label: 'NAME', key: 'yard_name' },
    { label: 'ADDRESS', key: 'yard_address' },
    { label: 'STATUS', key: 'status' },
    { label: 'CREATED AT', key: 'created_at' },
])


const props = defineProps({
    motels: {
        type: Object,
    },
    roles: Array,
    permissions: Array,
    queryLimit: Number
})
const showModalAddUser = (id) => {
    motelId.value = id
    modalAddUser.value = !modalAddUser.value
}
const storeMotelUser = async () => {
    const result = await storeItem(`add_motel_user/${motelId.value}`, userFormData)
    if (result.success) {
        router.get(route('motel.index'), {
            preserveState: true,
            preserveScroll: true
        })
        console.log("success storing")
    } else {
        console.log("error storing: ", result.error)
    }
}
const handleSubmit = async () => {
    formData.post(route('motel.store'), {
        preserveState: true,
        onSuccess: () => {
            console.log("success")
        }
    })
    showModal.value = false

}
const handleUpdate = async () => {
    formData.patch(route('motel.update', itemId.value), formData, {
        preserveState: true,
        onSuccess: () => {
            console.log("update success")
        }
    })
    showModalEdit.value = false

}
const editData = async (id) => {
    itemId.value = id
    const result = await fetchItem(`motel/${id}`)
    if (result.success) {
        formData.yard_name = result.data.yard_name
        formData.status = result.data.status
        formData.yard_address = result.data.yard_address
        showModalEdit.value = true
    } else {
        console.log(result.error)
    }

}
const limitQuery = (name, e) => {
    let qParams = {}
    let nParams = {}
    let params = {}
    if (props.queryName) {
        nParams['name'] = props.queryName
        qParams[name] = e

        params = { ...qParams, ...nParams }
    } else if (!props.queryName) {
        qParams[name] = e
        params = qParams
    } else {
        delete qParams[name]
        delete nParams['name']
    }
    router.get(route('motel.index', params))
}
const searchFieldQuery = (name, e) => {
    let qParams = {}
    let nParams = {}
    let params = {}
    if (props.queryLimit) {
        nParams[name] = e
        qParams['query'] = props.queryLimit

        params = { ...nParams, ...qParams }
    } else if (!props.queryLimit) {
        nParams[name] = e
        params = nParams
    } else {
        delete qParams[name]
        delete nParams['name']
    }
    router.get(route('motel.index', params))
}
const deleteData = (id) => {
    if (confirm('Are you sure you want to delete this yard?')) {
        router.delete(route('motel.destroy', id), {
            preserveState: true
        })
    }

}
onMounted(() => {
    console.log(props.roles)
})
</script>

<style lang="scss" scoped></style>