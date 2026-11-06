<script setup>
import { reactive, watch } from 'vue';
const props = defineProps({
student: { type: Object, default: null },
errors: { type: Object, default: () => ({}) },
});
const emit = defineEmits(['save', 'cancel']);
const blank = {
student_number: '',
first_name: '',
last_name: '',
email: '',
course: '',
year_level: '',
};
const form = reactive({ ...blank });
watch(
() => props.student,
(student) => {
Object.assign(form, student ? { ...student } : blank);
},
{ immediate: true }
);
</script>
<template>
<form class="card" @submit.prevent="emit('save', { ...form })">
<h2>{{ student ? 'Edit Student' : 'Add Student' }}</h2>
<label>Student Number</label>
<input v-model="form.student_number" type="text" />
<p v-if="errors.student_number" class="field-error">{{
errors.student_number[0] }}</p>
<label>First Name</label>
<input v-model="form.first_name" type="text" />
<p v-if="errors.first_name" class="field-error">{{
errors.first_name[0] }}</p>
<label>Last Name</label>
<input v-model="form.last_name" type="text" />
<p v-if="errors.last_name" class="field-error">{{ errors.last_name[0]
}}</p>
<label>Email</label>
<input v-model="form.email" type="email" />
<p v-if="errors.email" class="field-error">{{ errors.email[0] }}</p>
<label>Course</label>
<input v-model="form.course" type="text" />
<p v-if="errors.course" class="field-error">{{ errors.course[0] }}
</p>
<label>Year Level (1-6)</label>
<input v-model="form.year_level" type="number" />
<p v-if="errors.year_level" class="field-error">{{
errors.year_level[0] }}</p>
<div class="actions">
<button type="submit" class="btn primary">Save</button>
<button type="button" class="btn"
@click="emit('cancel')">Cancel</button>
</div>
</form>
</template>