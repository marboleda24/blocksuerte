<template>
    <div>
        <div class="intro-y box mt-5 mb-5">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Propuestas</h2>
                <div v-if="requirement.assigned_designer && requirement.state !== '5'"
                     v-permission="'design-requirements.proposal.create'"
                     class="w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                    <Tippy
                        tag="button"
                        content="Agregar propuesta"
                        class="btn btn-sm btn-secondary"
                        :options="{
                                placement: 'left'
                            }"
                        href="javascript:void(0)"
                        @click.native="openProposalModal"
                    >
                        <font-awesome-icon icon="plus"/>
                    </Tippy>
                </div>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto"
                     v-if="requirement.proposals.length > 0">
                    <table class="table table-sm table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>PRODUCTO (APROX)</th>
                            <th>MEDIDA</th>
                            <th>MATERIAL</th>
                            <th v-if="requirement.state === '5'">ARTE</th>
                            <th>DETALLES</th>
                            <th>ESTADO</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="proposal in requirement.proposals">
                            <td>{{ proposal.consecutive }}</td>
                            <td>{{ proposal.product }}</td>
                            <td>{{ proposal.measure }}</td>
                            <td>{{ proposal.material.material.name }}</td>
                            <td v-if="requirement.state === '5'">
                                {{ proposal.art.code }}
                            </td>
                            <td>{{ proposal.details }}</td>
                            <td class="text-center" v-html="$h.proposalState(proposal.state)"></td>
                            <td class="text-center">
                                <div class="flex flex-row">
                                    <button class="btn btn-primary" @click="openViewProposalModal(proposal)">
                                        <font-awesome-icon :icon="['far', 'eye']"/>
                                    </button>

                                    <button class="btn btn-primary ml-2" @click="printProposal(proposal.id)">
                                        <font-awesome-icon :icon="['fas', 'print']"/>
                                    </button>

                                    <button class="btn btn-primary ml-2" @click="cloneProposal(requirement.id, proposal.id)" v-if="requirement.state !== '5'">
                                        <font-awesome-icon :icon="['fas', 'clone']"/>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="alert alert-danger show flex items-center mb-2" role="alert">
                    <AlertTriangleIcon class="w-6 h-6 mr-2"/>
                    Aun no se registran propuestas
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="newProposalModalOpen" @close="closeModal" max-width=3xl>
            <template #title>
                Nueva propuesta
            </template>

            <template #content>
                <div class="grid grid-cols-3 gap-5">
                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Tipo de producto
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.newProposal.product_type_code.$error }"
                                @change="getLines(newProposal.product_type_code)"
                                v-model="newProposal.product_type_code">
                            <option v-for="product_type in product_types" :value="product_type.code">{{ product_type.name }}</option>
                        </select>

                        <template v-if="v$.newProposal.product_type_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.product_type_code.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Linea
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.newProposal.line_code.$error }"
                                @change="getSublines(newProposal.line_code)"
                                v-model="newProposal.line_code">
                            <option v-for="line in lines" :value="line.code">{{ line.name }}</option>
                        </select>

                        <template v-if="v$.newProposal.line_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.line_code.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Sublinea
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.newProposal.subline_code.$error }"
                                @change="getAnotherInputs(newProposal.line_code, newProposal.subline_code)"
                                v-model="newProposal.subline_code">
                            <option v-for="subline in sublines" :value="subline.code">{{ subline.name }}</option>
                        </select>

                        <template v-if="v$.newProposal.subline_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.subline_code.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Característica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.newProposal.feature_code.$error }"
                                v-model="newProposal.feature_code">
                            <option v-for="feature in features" :value="feature.code">{{ feature.name }}</option>
                        </select>

                        <template v-if="v$.newProposal.feature_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.feature_code.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Material
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <select class="form-select"
                                :class="{ 'border-danger': v$.newProposal.material_id.$error }"
                                v-model="newProposal.material_id">
                            <option v-for="material in materials" :value="material.id">
                                {{ material.material.name }}
                            </option>
                        </select>

                        <template v-if="v$.newProposal.material_id.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.material_id.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Medida
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <div class="input-group">
                            <select class="form-select"
                                    :class="{ 'border-danger': v$.newProposal.measurement_id.$error }"
                                    v-model="newProposal.measurement_id">
                                <option v-for="measurement in measurements" :value="measurement.id">
                                    {{ $h.denominationCreator(measurement.detail) }}
                                </option>
                            </select>
                            <div class="input-group-text cursor-pointer" @click="freshMeasurements(newProposal.line_code, newProposal.subline_code)">
                                <font-awesome-icon icon="rotate" spin/>
                            </div>
                        </div>

                        <template v-if="v$.newProposal.measurement_id.$error">
                            <ul class="mt-1">
                                <li class="text-danger"
                                    v-for="(error, index) of v$.newProposal.measurement_id.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Archivo 2D
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio Max(800x800)
                            </span>
                        </label>

                        <input type="file"
                               ref="file_2d"
                               accept="image/*"
                               @change="image2DFileChanged($event)"
                               class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary"
                               :class="{ 'border-danger': v$.newProposal.file2D.$error }"/>

                        <template v-if="v$.newProposal.file2D.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.file2D.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <div class="input-form col-span-3">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Detalles
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <textarea class="form-control"
                                  :class="{ 'border-danger': v$.newProposal.details.$error }"
                                  v-model="newProposal.details" cols="30" rows="1"
                                  @input="formatTextareaContent"></textarea>

                        <template v-if="v$.newProposal.details.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.newProposal.details.$errors"
                                    :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>
            </template>

            <template #footer>
                <button @click="closeModal()" type="button" class="btn btn-secondary mr-2">
                    Cancelar
                </button>

                <button @click.prevent="storeProposal(newProposal)" type="submit" class="btn btn-primary">
                    Guardar
                </button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="viewProposalModalOpen" @close="closeModal" max-width="5xl">
            <template #title>
                Visualización de Propuesta
            </template>

            <template #content>
                <div class="overflow-x-auto pb-5">
                    <table class="table table-bordered">
                        <thead>
                        <tr class="text-center">
                            <th class="whitespace-nowrap">ESTADO</th>
                            <th class="whitespace-nowrap">VER.</th>
                            <th class="whitespace-nowrap">REQUERIMIENTO</th>
                            <th class="whitespace-nowrap">PROPUESTA</th>
                            <th class="whitespace-nowrap">2D</th>
                            <th class="whitespace-nowrap">3D</th>
                            <th class="whitespace-nowrap">PLANOS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center">
                            <td v-html="$h.proposalState(viewProposal.state)"></td>
                            <td>{{ viewProposal.version }}</td>
                            <td>{{ requirement.id }}</td>
                            <td>ID {{ viewProposal.id }}</td>
                            <td>
                                <div class="flex flex-row">
                                    <input type="file"
                                           ref="proposal_2d_file"
                                           @change="proposal2DFileChanged($event)"
                                           accept="image/*"
                                           class="form-control border border-solid border-gray-300 block w-full text-sm text-slate-500 file:btn file:btn-primary">

                                    <button v-if="viewProposal.url2D.length > 0" class="btn btn-secondary ml-2" @click="download2D(viewProposal.path2D)">
                                        <font-awesome-icon icon="download"/>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-row justify-center" v-if="viewProposal.url3D.length > 0">
                                    <button class="btn btn-secondary">
                                        <font-awesome-icon icon="eye"/>
                                    </button>
                                    <button class="btn btn-secondary ml-2">
                                        <font-awesome-icon icon="download"/>
                                    </button>
                                </div>
                                <span v-else class="badge badge-danger">NO HAY ARCHIVOS</span>
                            </td>
                            <td>
                                <button
                                    v-if="viewProposal.blueprint"
                                    class="btn btn-secondary"
                                    @click="viewBlueprint(viewProposal.blueprint.miniature)">
                                    <font-awesome-icon icon="eye"/>
                                </button>

                                <span v-else class="badge badge-danger">NO HAY ARCHIVOS</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto pb-10 border-b" v-if="viewProposal.state === '7'">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">ARTE</th>
                                <th class="whitespace-nowrap">CODIGO</th>
                                <th class="whitespace-nowrap">PRODUCTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>{{ viewProposal.art.code }}</td>
                                <td>{{ viewProposal.art.product?.code }}</td>
                                <td>{{ viewProposal.art.product?.description }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-5 gap-4 mt-10 pb-5 border-b">
                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Tipo de producto
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="viewProposalForm.product_type_code"
                                v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                @change="getLines(viewProposalForm.product_type_code)">
                            <option v-for="product_type in product_types" :value="product_type.code">
                                {{ product_type.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Linea
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="viewProposalForm.line_code"
                                v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                @change="getSublines(viewProposalForm.line_code)">
                            <option v-for="line in lines" :value="line.code">
                                {{ line.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Sublinea
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-model="viewProposalForm.subline_code"
                                v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                @change="getAnotherInputs(viewProposalForm.line_code, viewProposalForm.subline_code)">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="subline in sublines" :value="subline.code">
                                {{ subline.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Característica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                v-model="viewProposalForm.feature_code">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="feature in features" :value="feature.code">
                                {{ feature.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Material
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>
                        <select class="form-select"
                                v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                v-model="viewProposalForm.material_id">
                            <option value="" selected disabled>Seleccione…</option>
                            <option v-for="material in materials" :value="material.id">
                                {{ material.material.name }}
                            </option>
                        </select>
                    </div>

                    <div class="input-form">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Medida
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Obligatorio
                            </span>
                        </label>

                        <div class="input-group">
                            <select class="form-select form-control"
                                    v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                    :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                                    v-model="viewProposalForm.measurement_id">
                                <option value="" selected disabled>Seleccione…</option>
                                <option v-for="measurement in measurements" :value="measurement.id">
                                    {{ $h.denominationCreator(measurement.detail) }}
                                </option>
                            </select>
                            <div class="input-group-text cursor-pointer" @click="freshMeasurements(newProposal.line_code, newProposal.subline_code)">
                                <font-awesome-icon icon="rotate" spin/>
                            </div>
                        </div>


                    </div>

                    <div class="input-form col-span-2">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Detalles
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Opcional
                            </span>
                        </label>

                        <textarea class="form-control resize-none"
                                  v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                  v-model="viewProposalForm.details" cols="30" rows="1"></textarea>
                    </div>

                    <div class="input-form col-span-2">
                        <label class="form-label w-full flex flex-col sm:flex-row">
                            Característica
                            <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">
                                Opcional
                            </span>
                        </label>

                        <textarea class="form-control resize-none"
                                  v-permission:has.disabled="'design-requirements.proposal.change-product'"
                                  v-model="viewProposalForm.features_detail" cols="30" rows="1"></textarea>
                    </div>

                    <button class="btn btn-primary"
                            v-permission:any="'design-requirements.proposal.new-version|design-requirements.proposal.change-product'"
                            :disabled="viewProposal.state === '2' || viewProposal.state === '3' || viewProposal.state === '4' || viewProposal.state === '7'"
                            @click="updateProposal(viewProposalForm)">
                        Guardar Cambios
                    </button>
                </div>


                <div class="overflow-x-auto mt-10 pb-10 border-b" v-if="viewProposal.versions.length > 0">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="4" class="whitespace-nowrap text-center col-span-4 uppercase">Versiones
                                Anteriores
                            </th>
                        </tr>
                        <tr class="text-center">
                            <th class="whitespace-nowrap">Identificador</th>
                            <th class="whitespace-nowrap">Version</th>
                            <th class="whitespace-nowrap">Fecha Generación</th>
                            <th class="whitespace-nowrap"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center" v-for="version in viewProposal.versions">
                            <td>{{ version.id }}</td>
                            <td>{{ version.version }}</td>
                            <td>{{ $h.formatDate(version.created_at, 'YYYY-MM-DD hh:mm a') }}</td>
                            <td>
                                <button class="btn btn-secondary" @click="viewOldVersionProposal(version.id)">
                                    <font-awesome-icon icon="print" class="mr-2"/>
                                    Imprimir
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="chat__box box mt-10 border border-solid border-gray-300">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto">Comentarios</h2>

                        <div class="w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                            <Tippy
                                tag="button"
                                content="Visualizar logs"
                                class="btn btn-sm btn-secondary"
                                :options="{
                                placement: 'left'
                            }"
                                href="javascript:void(0)"
                                @click.native=""
                            >
                                <font-awesome-icon icon="list-ol"/>
                            </Tippy>
                        </div>
                    </div>

                    <div class="overflow-y-scroll scrollbar-hidden px-5 pt-5 flex-1 h-56 max-h-56"
                         ref="proposal_comments">
                        <template v-for="log in viewProposal.logs">

                            <template v-if="log.created_user.id === $page.props.user.id && log.type === 'comment'">
                                <div class="chat__box__text-box flex items-end float-right mb-4">
                                    <div class="bg-primary px-4 py-3 text-white rounded-l-md rounded-t-md">
                                        {{ log.description }}
                                        <div class="mt-1 text-xs text-white text-opacity-80">
                                            {{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}
                                        </div>
                                    </div>
                                    <div class="w-10 h-10 hidden sm:block flex-none image-fit relative ml-2">
                                        <Tippy tag="img"
                                               :content="log.created_user.name"
                                               class="rounded-full"
                                               :src="log.created_user.profile_photo_url"
                                               :options="{ placement: 'top' }"
                                        />
                                    </div>
                                </div>
                                <div class="clear-both"></div>
                            </template>

                            <template v-else-if="log.created_user.id !== $page.props.user.id && log.type === 'comment'">
                                <div class="chat__box__text-box flex items-end float-left mb-4">
                                    <div class="w-10 h-10 hidden sm:block flex-none image-fit relative mr-2">
                                        <Tippy tag="img"
                                               :content="log.created_user.name"
                                               class="rounded-full"
                                               :src="log.created_user.profile_photo_url"
                                               :options="{ placement: 'top' }"
                                        />
                                    </div>
                                    <div
                                        class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md">
                                        {{ log.description }}
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $h.formatDate(log.created_at, 'YYYY-MM-DD hh:mm a') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="clear-both"></div>
                            </template>
                        </template>
                    </div>

                    <div
                        class="pt-4 pb-10 sm:py-4 flex items-center border-t border-slate-200/60 dark:border-darkmode-400">
                        <textarea
                            class="chat__box__input form-control dark:bg-darkmode-600 h-12 resize-none border-transparent px-5 py-3 shadow-none focus:border-transparent focus:ring-0"
                            @keypress.enter.prevent="addComment"
                            v-model="comment"
                            rows="1"
                            placeholder="Escribe tu mensaje…"></textarea>

                        <a href="javascript:;"
                           @click="addComment"
                           class="w-8 h-8 sm:w-10 sm:h-10 block bg-primary text-white rounded-full flex-none flex items-center justify-center mr-5">
                            <font-awesome-icon :icon="['far', 'paper-plane']"/>
                        </a>
                    </div>
                </div>
            </template>

            <template #footer>
                <template v-if="viewProposal.state === '1'">
                    <button class="btn btn-outline-success mr-2"
                            v-permission="'design-requirements.proposal.create'"
                            @click="changeState(2)">
                        Solicitar 3D
                    </button>

                    <button class="btn btn-outline-success mr-2"
                            v-permission="'design-requirements.proposal.create'"
                            @click="changeState(3)">
                        Solicitar Planos
                    </button>
                </template>

                <template v-if="viewProposal.state === '1' || viewProposal.state === '5'">
                    <button class="btn btn-outline-success mr-2"
                            v-permission="'design-requirements.proposal.create'"
                            @click="changeState(4)">
                        Solicitar Aprobación
                    </button>
                </template>

                <button class="btn btn-outline-danger mr-2"
                        v-if="viewProposal.state !== '7'"
                        v-permission="'design-requirements.proposal.cancel'"
                        @click="changeState(0)">
                    Anular
                </button>

                <template v-if="viewProposal.state === '4'">
                    <button class="btn btn-outline-warning mr-2"
                            @click="changeState(5)">
                        Solicitar Corrección
                    </button>

                    <button class="btn btn-outline-primary mr-2"
                            @click="changeState(6)">
                        Aprobar
                    </button>
                </template>

                <button @click="closeModal()" type="button" class="btn btn-outline-secondary ml-auto">
                    Cerrar
                </button>
            </template>

        </jet-dialog-modal>
    </div>
</template>

<script lang="jsx">
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        requirement: Object,
        product_types: Array,
    },

    components: {
        FontAwesomeIcon,
        JetDialogModal
    },

    validations() {
        return {
            newProposal: {
                product_type_code: {required},
                line_code: {required},
                subline_code: {required},
                feature_code: {required},
                material_id: {required},
                measurement_id: {required},
                file2D: {required},
                details: {required},
            },
        }
    },

    data() {
        return {
            lines: [],
            sublines: [],
            features: [],
            materials: [],
            measurements: [],
            newProposalModalOpen: false,
            viewProposalModalOpen: false,
            newProposal: {
                feature_code: this.requirement.feature.code,
                material_id: this.requirement.material.id,
                measurement_id: this.requirement.measurement.id,
                details: '',
                file2D: '',
            },
            viewProposal: {},
            viewProposalForm: {
                product_type_code: '',
                line_code: '',
                subline_code: '',
                feature_code: '',
                material_id: '',
                measurement_id: '',
                details: '',
                file2D: '',
                features_detail: '',
            },
            comment: ''

        }
    },

    methods: {
        openProposalModal() {
            this.newProposalModalOpen = true
        },

        openViewProposalModal(proposal) {
            this.viewProposal = proposal
            this.viewProposalModalOpen = true
            this.viewProposalForm = {
                product_type_code: proposal.product_type.code,
                line_code: proposal.line.code,
                subline_code: proposal.subline.code,
                feature_code: proposal.feature.code,
                material_id: proposal.material.id,
                measurement_id: proposal.measurement.id,
                details: proposal.details,
                file2D: '',
                features_detail: proposal.features_detail,
                type: ''
            }
            this.getLines(proposal.product_type.code)
            this.getSublines(proposal.line.code)
            this.getAnotherInputs(proposal.line.code, proposal.subline.code)
        },

        closeModal() {
            this.v$.newProposal.$reset();
            this.newProposalResetForm();
            this.newProposalModalOpen = false
            this.viewProposalModalOpen = false
        },

        getLines(product_type_code){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-lines'), {
                params: {
                    product_type: product_type_code
                }
            }).then(resp => {
                this.lines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getSublines(line_code) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-sublines'), {
                params: {
                    line_code: line_code
                }
            }).then(resp => {
                this.sublines = resp.data
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        getAnotherInputs(line_code, subline_code) {
            this.resetChangeSubline()

            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-other-inputs'), {
                params: {
                    line_code: line_code,
                    subline_code: subline_code
                }
            }).then(resp => {
                this.features = resp.data.features
                this.materials = resp.data.materials
                this.measurements = resp.data.measurements
                this.$swal.close();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        freshMeasurements(line_code, subline_code){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Obteniendo información…',
                text: 'Este proceso puede tardar unos segundos…',
            });

            axios.get(route('codes.get-measurements'), {
                params: {
                    line_code: line_code,
                    subline_code: subline_code
                }
            }).then(resp => {
                this.measurements = resp.data
                this.$swal.close();
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
            })
        },

        resetChangeSubline() {
            this.features = []
            this.materials = []
            this.measurements = []
        },

        printProposal(id) {
            let url = route('design-requirements.proposal-print', id);
            window.open(url, '_blank').focus();
        },

        viewOldVersionProposal(id) {
            let url = route('design-requirements.old-version-proposal-print', id);
            window.open(url, '_blank').focus();
        },

        storeProposal(form) {
            this.v$.newProposal.$touch()
            if (!this.v$.newProposal.$invalid) {
                const formData = new FormData();

                Object.keys(form).forEach(key => formData.append(key, form[key]));

                let file = this.$refs.file_2d.files[0];
                formData.append('file_2d', file);
                formData.append('id', this.requirement.id);

                axios.post(route('design-requirements.proposal-store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    this.$swal({
                        title: '¡Éxito!',
                        text: "Propuesta creada con éxito",
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    })
                    this.$emit('store', {logs: resp.data.logs, proposals: resp.data.proposals})
                    this.closeModal()
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: 'Hubo un error procesando la solicitud.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        cloneProposal(header_id, id){
            this.$swal({
                icon: 'question',
                title: '¿Clonar propuesta?',
                text: "¡Recuerde adjuntar el archivo 2D!",
                showCancelButton: true,
                confirmButtonText: '¡Si, continuar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Reemplazando información…',
                        text: 'Este proceso puede tardar unos segundos…',
                    });

                    axios.post(route('design-requirements.clone-proposal'), {
                        header_id: header_id,
                        id: id
                    }).then(resp => {
                        this.$swal({
                            title: '¡Éxito!',
                            text: "Propuesta clonada con éxito",
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        })
                        this.$emit('store', {logs: resp.data.logs, proposals: resp.data.proposals})
                        this.closeModal()
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: err.response.data,
                            confirmButtonText: 'Aceptar',
                            timer: 6000
                        });
                    })
                }
            })
        },

        image2DFileChanged(event) {
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.newProposal.file2D = ''
            }
            this.newProposal.file2D = files[0];
        },

        proposal2DFileChanged(event) {
            const files = event.target.files || event.dataTransfer.files;
            if (!files.length || files.length === 0) {
                this.viewProposalForm.file2D = ''
            }
            this.viewProposalForm.file2D = files[0];
        },

        newProposalResetForm() {
            this.newProposal = {
                feature_code: this.requirement.feature.code,
                material_id: this.requirement.material.id,
                measurement_id: this.requirement.measurement.id,
                details: '',
                file2D: ''
            }
        },

        addComment() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('design-requirements.proposal.add-comment'), {
                id: this.viewProposal.id,
                comment: this.comment
            }).then(resp => {
                this.viewProposal.logs = resp.data
                this.comment = ''
                this.endScrollComments()
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.error(err);
            })
        },

        endScrollComments() {
            this.$refs.proposal_comments.scrollTop = this.$refs.proposal_comments.scrollHeight;
        },

        updateProposal(form) {
            if (this.viewProposal.state === '1' || this.viewProposal.state === '5') {
                this.$swal({
                    icon: 'question',
                    title: 'Guardar o versionar cambios',
                    text: 'Por favor, seleccione una opción',
                    input: 'select',
                    inputOptions: {
                        none: 'Seleccione…',
                        changes: 'Cambio de información',
                        version: 'Generar nueva versión'
                    },
                    inputAttributes: {
                        required: true
                    },
                    customClass: {
                        input: 'form-select w-full',
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary mr-2'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Continuar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (inputValue) => {
                        if (inputValue === 'none') {
                            return 'Debes seleccionar una opción…'
                        } else if (inputValue === 'version' && this.viewProposalForm.file2D.length === 0) {
                            return 'Para generar una nueva version es necesario que adjuntes el archivo 2D actualizado'
                        }
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {


                        this.$swal({
                            iconHtml: this.$h.loadIcon(),
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Actualizando información…',
                            text: 'Este proceso puede tardar unos segundos…',
                        });

                        const formData = new FormData();
                        Object.keys(form).forEach(key => formData.append(key, form[key]));

                        let file = this.$refs.proposal_2d_file.files[0];
                        formData.append('file_2d', file);
                        formData.append('requirement_id', this.requirement.id);
                        formData.append('id', this.viewProposal.id);
                        formData.append('type', inputValue.value);

                        axios.post(route('design-requirements.proposal.update'), formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }).then(resp => {
                            this.$emit('UpdateProposals', {logs: resp.data.logs, proposals: resp.data.proposals})
                            this.viewProposal = resp.data.proposal
                            this.$swal.close();
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: 'Hubo un error procesando la solicitud.',
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err);
                        })
                    }
                })


            } else {
                this.$swal({
                    icon: 'error',
                    title: '¡Acción Prohibida!',
                    text: 'Solo puedes actualizar o crear nuevas versiones de una propuesta cuando están en los estados EN PROCESO o POR CORREGIR.',
                    confirmButtonText: 'Aceptar',
                });
            }
        },

        viewBlueprint(imgBase64) {
            this.$swal({
                title: "Miniatura plano",
                imageUrl: imgBase64,
                imageAlt: '2D-image',
                confirmButtonText: 'Cerrar'
            })
        },

        changeState(state) {
            let title = "";
            let text = "";

            switch (state) {
                case 2:
                    title = "¿Solicitar 3D?";
                    text = this.requirement.render === 'yes'
                        ? "La propuesta sera enviada al area de 3D para la creación del modelo 3D"
                        : "El vendedor NO solicito modelado 3D, ¿estas segur@ de enviar la propuesta al area de 3D?"
                    break;
                case 3:
                    title = "¿Solicitar Planos?";
                    text = "El requerimiento sera enviado al area de planos, ¿Deseas continuar?";
                    break;
                case 4:
                    title = "¿Enviar para aprobación?";
                    text = "La propuesta sera enviada al area comercial para su verificación y aprobación del cliente, ¿Deseas continuar?";
                    break;
                case 5:
                    title = "¿Solicitar corrección?";
                    text = "La propuesta sera enviada al area de diseño grafico para la corrección. Recuerda enviar tus correcciones y comentarios en el area de comentarios de la propuesta, ¿Deseas continuar?";
                    break;
                case 6:
                    title = "¿Aprobar?";
                    text = "La propuesta sera enviada al area de diseño grafico y se creara un nuevo arte, ¿Deseas continuar?";
                    break;
            }

            if (state === 2 || state === 4 || state === 5 || state === 6) {
                this.$swal({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '¡Si, Continuar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(route('design-requirements.proposal.change-state'), {
                            id: this.viewProposal.id,
                            state: state,
                            requirement_id: this.requirement.id
                        }).then(resp => {
                            this.$swal({
                                title: '¡Éxito!',
                                text: 'Propuesta actualizada con éxito',
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                            })

                            this.$emit('UpdateProposals', {logs: resp.data.logs, proposals: resp.data.proposals})
                            this.viewProposal = resp.data.proposal
                            this.closeModal()
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: err.response.data,
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err);
                        })
                    }
                })

            }else if(state === 0){
                this.$swal({
                    title: '¿Anular Propuesta?',
                    text: 'la propuesta sera anulada y no podrás tener mas gestion sobre la misma, solo un administrador puede revertir esta acción. Por favor escribe una justificación',
                    icon: 'question',
                    input: 'textarea',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Si, Anular',
                    inputValidator: (inputValue) => {
                        return !inputValue && 'La justificación es obligatoria'
                    }
                }).then((inputValue) => {
                    if (inputValue.value) {
                        axios.post(route('design-requirements.proposal.change-state'), {
                            id: this.viewProposal.id,
                            state: state,
                            requirement_id: this.requirement.id,
                            justify: inputValue.value
                        }).then(resp => {
                            this.$swal({
                                title: '¡Éxito!',
                                text: 'Propuesta actualizada con éxito',
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                            })

                            this.$emit('UpdateProposals', {logs: resp.data.logs, proposals: resp.data.proposals})
                            this.viewProposal = resp.data.proposal
                            this.closeModal()
                        }).catch(err => {
                            this.$swal({
                                icon: 'error',
                                title: '¡Ups!',
                                text: 'Hubo un error procesando la solicitud.',
                                confirmButtonText: 'Aceptar',
                            });
                            console.log(err);
                        })
                    }
                })
            }
            else if (state === 3) {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Validando Planos…',
                    text: 'Este proceso puede tardar unos segundos…',
                });

                axios.get(route('design-requirements.proposal.validate-blueprint', this.viewProposal.id)).then(resp => {
                    if (resp.data.state === 'blueprint-exist') {
                        this.$swal({
                            icon: 'info',
                            title: '¡Plano Registrado!',
                            text: 'Este producto ya tiene un plano registrado por lo cual ha sido asignado automáticamente, ' +
                                'si por algún motivo se cambia el producto de la propuesta debes solicitar nuevamente el plano',
                            confirmButtonText: 'Aceptar',
                        })

                        this.$emit('UpdateProposals', {logs: resp.data.logs, proposals: resp.data.proposals})
                        this.viewProposal = resp.data.proposal
                        this.closeModal()
                    } else {
                        this.$swal({
                            title: 'Producto sin planos',
                            text: 'Este producto aun no cuenta con planos, ¿Quieres solicitar el plano al area encargada?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: '¡Si, Solicitar!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.post(route('design-requirements.proposal.change-state'), {
                                    id: this.viewProposal.id,
                                    state: state,
                                    requirement_id: this.requirement.id
                                }).then(resp => {
                                    this.$swal({
                                        title: '¡Éxito!',
                                        text: 'Propuesta actualizada con éxito',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar',
                                    })

                                    this.$emit('UpdateProposals', {
                                        logs: resp.data.logs,
                                        proposals: resp.data.proposals
                                    })
                                    this.viewProposal = resp.data.proposal
                                    this.closeModal()
                                }).catch(err => {
                                    this.$swal({
                                        icon: 'error',
                                        title: '¡Ups!',
                                        text: err.response.data,
                                        confirmButtonText: 'Aceptar',
                                    });
                                    console.log(err);
                                })
                            }
                        })
                    }
                })
            }
        },

        download2D(path) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando Archivo…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('design-requirements.download-file'), {
                path: path
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', `2D.${path.split('.').pop()}`);
                document.body.appendChild(link);
                link.click();
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.error(err);
            })
        },

        formatTextareaContent() {
            this.newProposal.details = this.newProposal.details.replace(/@/g, '\n');
        },
    }
}
</script>
