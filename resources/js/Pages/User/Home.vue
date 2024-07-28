<template>

    <Head title="Motel" />

    <AuthenticatedLayout>
        <div class="md:pl-12 md:pr-4 content-area">
            <div>
                <RoomDatatable :data="props.rooms" :columns="columns" :pagination="props.rooms"
                    @limit-query="limitQuery" @search-field-query="searchFieldQuery" @delete="deleteData"
                    @edit="editData" @checkout="checkout" :query-limit="props.queryLimit" :route-create="createRoute"
                    :query-name="props.queryName" :action="false" :checkout="false" @check-in="checkIn"
                    @recheck-in-checked-in="recheckInCheckedIn" />
            </div>
        </div>
        <!-- <div>
            <NavBottom />
        </div> -->
    </AuthenticatedLayout>
</template>

<script setup>
import SaveInUse from '@/Components/SaveInUse.vue'
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NavBottom from "@/Components/NavBottom.vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from "vue"
import { router, Head, useForm, usePage, Link } from "@inertiajs/vue3";
import RoomDatatable from "@/Components/RoomDatatable.vue";

const motelId = ref('')
const next = ref(false)
const recheckIn = ref(false)
const columns = ref([
    { label: 'ROOM #', key: 'tractor_number' }
])
const props = defineProps({
    rooms: {
        type: Object,
    },
})
const checkout = () => {
    console.log("checkout")
}
const checkIn = (id) => {
    next.value = true
    motelId.value = id
}
const recheckInCheckedIn = (id) => {
    recheckIn.value = true
    next.value = true
    motelId.value = id
}
onMounted(() => {
    console.log(props.rooms)
})
</script>

<style scoped>
.content-area {
    height: calc(100vh - 4rem);
}
</style>