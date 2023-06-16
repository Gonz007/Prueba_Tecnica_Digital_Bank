using System;
using System.Collections.Generic;
using System.Runtime.Serialization;
using System.ServiceModel;

namespace pruebaTecnicaWcf
{
    [ServiceContract]
    public interface IService1
    {
        [OperationContract]
        string GetData(int value);

        [OperationContract]
        CompositeType GetDataUsingDataContract(CompositeType composite);

        [OperationContract]
        void InsertarUsuario(string nombre, DateTime fechaDeNacimiento, string sexo);

        [OperationContract]
        void ActualizarUsuario(int id, string nombre, DateTime fechaDeNacimiento, string sexo);

        [OperationContract]
        void EliminarUsuario(int id);

        [OperationContract]
        List<Usuario> LeerUsuarios();

        [OperationContract]
        string AutenticarUsuario(string nombre, string contrasena);


    }
    [DataContract]
    public class Usuario
    {
        [DataMember]
        public int Id { get; set; }

        [DataMember]
        public string Nombre { get; set; }

        [DataMember]
        public DateTime FechaDeNacimiento { get; set; }

        [DataMember]
        public string Sexo { get; set; }
    }
    [DataContract]
    public class CompositeType
    {
        bool boolValue = true;
        string stringValue = "";

        [DataMember]
        public bool BoolValue
        {
            get { return boolValue; }
            set { boolValue = value; }
        }

        [DataMember]
        public string StringValue
        {
            get { return stringValue; }
            set { stringValue = value; }
        }
    }
}
