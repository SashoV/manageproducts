<script>
import useVuelidate from "@vuelidate/core";
import {
  required,
  integer,
  decimal,
  minValue,
  alpha,
  helpers,
} from "@vuelidate/validators";
import Footer from "@/components/Footer.vue";

export default {
  setup() {
    return { v$: useVuelidate() };
  },
  data() {
    return {
      productTypes: [],
      attributes: [],
      obj: null,
      description: null,
      formData: {
        sku: "",
        name: "",
        price: "",
        type: "",
      },
    };
  },
  validations() {
    return {
      formData: this.rules,
    };
  },
  methods: {
    getProductTypes() {
      fetch(`${import.meta.env.VITE_APP_BACKEND_API_URL}/productTypes`)
        .then((response) => response.json())
        .then((types) => {
          this.productTypes = types;
        });
    },
    onFormChange(event) {
      const defaultKeys = ["sku", "name", "price", "type"];
      const defaultEntries = Object.entries(this.formData).filter(([key]) =>
        defaultKeys.includes(key)
      );
      this.formData = Object.fromEntries(defaultEntries);
      this.obj = this.productTypes.find(
        (x) => x.type_name == event.target.value
      );
      this.attributes = this.obj.spec_attr_details;
      Object.entries(this.attributes).forEach(([key, value]) => {
        this.formData[value.a_name] = "";
      });
      this.description = "*" + this.obj.spec_attr_description;
    },
    saveProduct(event) {
      this.v$.$validate();
      if (!this.v$.$error) {
        const requestOptions = {
          method: "POST",
          mode: "cors",
          cache: "default",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify(this.formData),
        };
        fetch(
          `${import.meta.env.VITE_APP_BACKEND_API_URL}/saveProduct`,
          requestOptions
        )
          .then((response) => response.json())
          .then(() => {
            this.$router.push({ name: "home" })
          })
      } else {
        //alert("validation error");
      }
    },
  },
  mounted() {
    this.getProductTypes();
  },
  components: {
    Footer,
  },
  computed: {
    rules() {
      let r = {
        sku: {
          required: helpers.withMessage(
            "Please, submit required data",
            required
          ),
          isUnique: helpers.withAsync((value) => {
            this.v$.formData.sku.isUnique.$error = false;
            this.v$.formData.sku.isUnique.$response = false;
            this.v$.formData.sku.isUnique.$invalid = false;
            this.v$.formData.sku.isUnique.$pending = true;
            this.v$.formData.sku.isUnique.$params = { type: "isUnique" };
            this.v$.formData.sku.isUnique.$message = "This SKU already exists";

            if (value === "") return true;
            const requestOptions = {
              method: "POST",
              mode: "cors",
              cache: "default",
              headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ sku: value }),
            };
            fetch(
              `${import.meta.env.VITE_APP_BACKEND_API_URL}/checkIsSkuUnique`,
              requestOptions
            )
              .then((response) => response.json())
              .then((data) => {
                if (data.valid == false) {
                  //console.log("taken");
                  this.v$.formData.sku.isUnique.$error = true;
                  this.v$.formData.sku.isUnique.$invalid = true;
                  this.v$.formData.sku.isUnique.$pending = true;
                  this.v$.formData.sku.isUnique.$response = false;
                  return false;
                }
                //console.log("ok");
                this.v$.formData.sku.isUnique.$error = false;
                this.v$.formData.sku.isUnique.$response = true;
                this.v$.formData.sku.isUnique.$invalid = false;
                this.v$.formData.sku.isUnique.$pending = false;
                return true;
              });
          }),
        },
        name: {
          required: helpers.withMessage(
            "Please, submit required data",
            required
          ),
        },
        price: {
          required: helpers.withMessage(
            "Please, submit required data",
            required
          ),
          minValue: minValue(0),
          decimal: helpers.withMessage(
            "Please, provide the data of indicated type",
            decimal
          ),
        },
        type: {
          required: helpers.withMessage("Please, select one option", required),
          alpha: helpers.withMessage(
            "Please, provide the data of indicated type",
            alpha
          ),
        },
      };
      Object.entries(this.attributes).forEach(([key, value]) => {
        if (value.a_type == "integer") {
          r[value.a_name] = {
            required: helpers.withMessage(
              "Please, submit required data",
              required
            ),
            integer: helpers.withMessage(
              "Please, provide the data of indicated type",
              integer
            ),
          };
        }
      });
      return r;
    },
  },
};
</script>

<template>
  <div class="container-fluid">
    <div class="main">
      <div
        class="
          header
          d-flex
          justify-content-between
          align-items-end
          px-2
          pb-3
          pt-5
          border-bottom border-2
        "
      >
        <h3 class="mb-0">Add Product</h3>
        <div class="d-block">
          <button @click="saveProduct" class="btn btn-primary btn-sm m-1">
            Save
          </button>
          <router-link to="/" class="btn btn-danger btn-sm m-1"
            >Cancel</router-link
          >
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-4">
          <form id="product_form">
            <div class="row align-items-center mt-3">
              <div class="col-md-2">
                <label for="sku" class="col-form-label">SKU</label>
              </div>
              <div class="col-md-10">
                <input
                  v-model.trim="v$.formData.sku.$model"
                  type="text"
                  id="sku"
                  name="sku"
                  class="form-control"
                  :class="{ 'is-invalid': v$.formData.sku.$error }"
                />
                <div class="invalid-feedback">
                  <span
                    v-if="v$.formData.sku.$error && v$.formData.sku.$errors[0]"
                  >
                    {{ v$.formData.sku.$errors[0].$message }}
                  </span>
                </div>
              </div>
            </div>
            <div class="row align-items-center mt-3">
              <div class="col-md-2">
                <label for="name" class="col-form-label">Name</label>
              </div>
              <div class="col-md-10">
                <input
                  v-model.trim="v$.formData.name.$model"
                  :class="{ 'is-invalid': v$.formData.name.$error }"
                  type="text"
                  id="name"
                  name="name"
                  class="form-control"
                />
                <div class="invalid-feedback">
                  <span v-if="v$.formData.name.$error">
                    {{ v$.formData.name.$errors[0].$message }}
                  </span>
                </div>
              </div>
            </div>
            <div class="row align-items-center mt-3">
              <div class="col-md-2">
                <label for="price" class="col-form-label">Price ($)</label>
              </div>
              <div class="col-md-10">
                <input
                  v-model.trim="v$.formData.price.$model"
                  type="number"
                  id="price"
                  name="price"
                  class="form-control"
                  :class="{ 'is-invalid': v$.formData.price.$error }"
                />
                <div class="invalid-feedback">
                  <span v-if="v$.formData.price.$error">
                    {{ v$.formData.price.$errors[0].$message }}
                  </span>
                </div>
              </div>
            </div>
            <div class="row align-items-center mt-3">
              <div class="col-md-4">
                <label for="productType" class="col-form-label"
                  >Type Switcher</label
                >
              </div>
              <div class="col-md-8">
                <select
                  class="form-select"
                  :class="{ 'is-invalid': v$.formData.type.$error }"
                  id="productType"
                  name="type"
                  v-model.trim="v$.formData.type.$model"
                  @change="onFormChange($event)"
                  aria-label="Default select example"
                >
                  <option disabled selected>Type Switcher</option>
                  <option
                    v-for="type in productTypes"
                    :key="type"
                    :value="type.type_name"
                  >
                    {{ type.type_string }}
                  </option>
                </select>
                <div class="invalid-feedback">
                  <span v-if="v$.formData.type.$error">
                    {{ v$.formData.type.$errors[0].$message }}
                  </span>
                </div>
              </div>
            </div>
            <div
              class="row align-items-center mt-3"
              v-for="item in attributes"
              :key="item"
            >
              <div class="col-md-2">
                <label :for="item.a_name" class="col-form-label"
                  ><span class="text-capitalize">{{ item.a_name }}</span> ({{
                    obj.unit
                  }})</label
                >
              </div>
              <div class="col-md-10">
                <input
                  v-model.trim="v$.formData[item.a_name].$model"
                  type="text"
                  :id="item.a_name"
                  :name="item.a_name"
                  class="form-control"
                  :class="{ 'is-invalid': v$.formData[item.a_name].$error }"
                />
                <div class="invalid-feedback">
                  <span v-if="v$.formData[item.a_name].$error">
                    {{ v$.formData[item.a_name].$errors[0].$message }}
                  </span>
                </div>
              </div>
            </div>
            <p class="mt-4">{{ this.description }}</p>
          </form>
        </div>
      </div>
    </div>
    <div class="position-relative">
      <Footer />
    </div>
  </div>
</template>