using Microsoft.IdentityModel.Tokens;
using System;
using System.Collections.Generic;
using System.Data;
using System.Data.SqlClient;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;

namespace pruebaTecnicaWcf
{
    public class Service1 : IService1
    {
        public string GetData(int value)
        {
            return string.Format("You entered: {0}", value);
        }
        public CompositeType GetDataUsingDataContract(CompositeType composite)
        {
            if (composite == null)
            {
                throw new ArgumentNullException("composite");
            }
            if (composite.BoolValue)
            {
                composite.StringValue += "Suffix";
            }
            return composite;
        }

        String conexion = "Data Source=JANNA\\SQLEXPRESS;Initial Catalog=MiBaseDeDatos;MultipleActiveResultSets=True;Connect Timeout=100;Encrypt=False;Integrated Security=True";
        public List<Usuario> LeerUsuarios()
        {
            List<Usuario> usuarios = new List<Usuario>();

            using (SqlConnection conn = new SqlConnection(conexion))
            {
                conn.Open();

                using (SqlCommand cmd = new SqlCommand("LeerUsuarios", conn))
                {
                    cmd.CommandType = CommandType.StoredProcedure;

                    using (SqlDataReader reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            Usuario usuario = new Usuario
                            {
                                Id = reader.GetInt32(reader.GetOrdinal("Id")),
                                Nombre = reader.GetString(reader.GetOrdinal("Nombre")),
                                FechaDeNacimiento = reader.GetDateTime(reader.GetOrdinal("FechaDeNacimiento")),
                                Sexo = reader.GetString(reader.GetOrdinal("Sexo"))
                            };
                            usuarios.Add(usuario);
                        }
                    }
                }
            }
            InsertarLog(DateTime.Now, "Se leyeron los usuarios");
            return usuarios;
        }
        public void InsertarUsuario(string nombre, DateTime fechaDeNacimiento, string sexo)
        {
            using (SqlConnection conn = new SqlConnection(conexion))
            {
                conn.Open();

                using (SqlCommand cmd = new SqlCommand("InsertarUsuario", conn))
                {
                    cmd.CommandType = CommandType.StoredProcedure;

                    cmd.Parameters.Add("@Nombre", SqlDbType.NVarChar).Value = nombre;
                    cmd.Parameters.Add("@FechaDeNacimiento", SqlDbType.Date).Value = fechaDeNacimiento;
                    cmd.Parameters.Add("@Sexo", SqlDbType.Char).Value = sexo;
                    cmd.ExecuteNonQuery();
                }
                InsertarLog(DateTime.Now, $"Usuario insertado: {nombre}");
            }
        }
        public void ActualizarUsuario(int id, string nombre, DateTime fechaDeNacimiento, string sexo)
        {
            using (SqlConnection conn = new SqlConnection(conexion))
            {
                conn.Open();

                using (SqlCommand cmd = new SqlCommand("ActualizarUsuario", conn))
                {
                    cmd.CommandType = CommandType.StoredProcedure;

                    cmd.Parameters.Add("@Id", SqlDbType.Int).Value = id;
                    cmd.Parameters.Add("@Nombre", SqlDbType.NVarChar).Value = nombre;
                    cmd.Parameters.Add("@FechaDeNacimiento", SqlDbType.Date).Value = fechaDeNacimiento;
                    cmd.Parameters.Add("@Sexo", SqlDbType.Char).Value = sexo;
                    cmd.ExecuteNonQuery();
                }
                InsertarLog(DateTime.Now, $"Usuario actualizado: {nombre}");
            }
        }
        public void EliminarUsuario(int id)
        {
            using (SqlConnection conn = new SqlConnection(conexion))
            {
                conn.Open();

                using (SqlCommand cmd = new SqlCommand("EliminarUsuario", conn))
                {
                    cmd.CommandType = CommandType.StoredProcedure;

                    cmd.Parameters.Add("@Id", SqlDbType.Int).Value = id;
                    cmd.ExecuteNonQuery();
                }
                InsertarLog(DateTime.Now, $"Usuario eliminado con ID: {id}");
            }
        }
        private void InsertarLog(DateTime fechaHora, string descripcion)
        {
            using (SqlConnection conn = new SqlConnection(conexion))
            {
                conn.Open();

                using (SqlCommand cmd = new SqlCommand("InsertarLog", conn))
                {
                    cmd.CommandType = CommandType.StoredProcedure;

                    cmd.Parameters.Add("@FechaHora", SqlDbType.DateTime).Value = fechaHora;
                    cmd.Parameters.Add("@Descripcion", SqlDbType.NVarChar).Value = descripcion;
                    cmd.ExecuteNonQuery();
                }
            }
        }
        private string SecretKey = "MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MA==";


        public string AutenticarUsuario(string nombre, string contrasena)
        {
            string nombreUsuarioValido = "admin";
            string contrasenaValida = "admin";

            if (nombre == nombreUsuarioValido && contrasena == contrasenaValida)
            {
                var tokenHandler = new JwtSecurityTokenHandler();
                var key = Encoding.ASCII.GetBytes(SecretKey);
                var tokenDescriptor = new SecurityTokenDescriptor
                {
                    Subject = new ClaimsIdentity(new[] { new Claim(ClaimTypes.Name, nombre) }),
                    Expires = DateTime.UtcNow.AddMinutes(7),

                    SigningCredentials = new SigningCredentials(new SymmetricSecurityKey(key), SecurityAlgorithms.HmacSha256Signature)
                };
                var token = tokenHandler.CreateToken(tokenDescriptor);
                return tokenHandler.WriteToken(token);
            }
            else
            {
                return "Error: usuario o contraseña inválidos";
            }
        }

    }
}
